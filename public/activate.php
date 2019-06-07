<?php 
require '../init.php';
require PACKAGE_PATH;
require LIB_PATH.DS.'class.upload'.DS.'class.upload.php';
if (!$session->isLoggedIn()) { Redirect::to("login.php?redirect=listproperty"); } 


use Rakit\Validation\Validator;
$validator = new Validator;

if(!Input::get('property')){
    $session->message("Could not find that property");
    Redirect::to("properties.php");
}  

// Find the previously inserted property
$property_id = (int) Input::get('property');
$property = Property::findById($property_id);

if(Input::exists()){

    $validation = $validator->make($_POST + $_FILES, [
        'price'          => 'required|numeric|min:3|max:10',
        'beds'           => 'required|numeric',
        'baths'          => 'required|numeric',
        'square_feet'    => 'required|numeric',
        'units'          => 'numeric',
        'available'      => 'required|date',
        'description'    => 'required',
        'owner'          => 'required|min:3',
        'contact_email'  => 'required|email',
        'contact_phone'  => 'required|numeric',
        'image_upload.*' => 'required|uploaded_file:10k,20000M,png,jpeg'  
    ]);

    $validation->setAliases([
        'square_feet'      => 'Plot size',
        'available'        => 'Date available',
        'description'      => 'Property description',
        'contact_email'    => 'Contact email',
        'contact_phone'    => 'contact phone',
        'image_upload'     => 'property photo'
    ]);

    $validation->setMessages([
        'required'              => ':attribute can not be empty',
        'available:required'    => ':attribute is required',
        'image_upload:required' => 'You need to upload a :attribute to continue'
    ]);

    // run the validation method
    $validation->validate();

    if($validation->fails()) {
        // handling errors
        $errors  = $validation->errors();
        $message = implode(", ", $errors->firstOfAll());
    }
	else{
        // Upload the Photo(s) first
        $pphoto = new PPhoto();

        $pphoto->attachFile($_FILES['image_upload'], $property_id);

        if($pphoto->uploadSuccess()){
            // Add the property to the database

            $property->beds      	    = (int)    Input::get('beds');
            $property->baths     	    = (float)  Input::get('baths');
            $property->terms      	    = (string) Input::get('rent_terms');
            $property->size      		= (string) Input::get('square_feet');
            $property->units            = (int)    Input::get('units');
            $property->price            = (int)    Input::get('price');
            $property->negotiable       = (int)    (Input::get('nego') === 'yes') ? true : false;
            $property->description      = (string) ucfirst(Input::get('description'));
            $property->cphoto           = (string) "";
            $property->contact_number   = (string) Input::get('contact_phone');
            $property->contact_email    = (string) Input::get('contact_email');
            $property->owner     	    = (string) Input::get('owner');
            $property->available        = (string) Input::get('available');
            $property->status           = (int)    2;            

        	if($property && $property->save()){
				// Save a corresponding entry to the database;
                $session->message("Your property has been added and is being reviewed, it will be listed as soon as we are done reviewing");
                Redirect::to('property.php?id='.$property->id);
            } else{
                $message = "Ooops something went wrong, try again";
            }
        }
        else{
            $message = implode("<br> ", $pphoto->uploadErrors());
        }
    }
}   

$page_title = "Activate listing";
?>


<?php include_layout_template('header.php'); ?>

	<?php 
        if (empty($property->address)) {
            echo "<h2>".strtoupper($property->type)." FOR ".strtoupper($property->market)."<br>";
            echo "in ".Location::findLocationOn($property->location_id)."&nbsp<small><a href=\"list.php?property=$property->id\">&nbsp;&nbsp;¶</a></small></h2>"; 
        }else{
            echo "<h2>FOR ".strtoupper($property->market)."<br>";
            echo $property->address.", ".Location::findLocationOn($property->location_id)."&nbsp<small><a href= \"list.php?property=$property->id\">&nbsp;&nbsp;¶</a></small></h2>";
        }
	?>
    
    <?php echo output_message($message, "danger"); ?>

    <form action="activate.php?property=<?php echo isset($property_id) ? $property_id: "";?>" enctype="multipart/form-data" method="POST" accept-charset="utf-8">

  		<h4>Details and Description</h4>

  		<?php
  			echo ($property->market == "rent") ?
	  			"<div>Rent Price</div>":
		  		"<div>Sale Price</div>";
	  	?>
	  	<input type="text" name="price" value="<?php echo escape(Input::get('price'));?>" placeholder="Enter amount" />
        <?php if($property->market == "sale"):?>
            <p style="margin-top: -10px;">
                <label><input type="checkbox" name="nego" value="yes" <?php if((isset($property) && $property->negotiable === 1) || Input::get('nego') === "yes"){echo "checked";} ?>> <span style="color:#11cc11;">Negotiable</span>
                </label>
            </p>
        <?php endif; ?>

	  	<div>Bedrooms</div>
	  	<?php echo create_form_select("beds", 5); ?>

	  	<div>Bathrooms</div>
	  	<?php echo create_form_select("baths", 5, "fractions"); ?>

	  	<div>Square Feet</div>
	  	<input type="text" name="square_feet" value="<?php echo escape(Input::get('square_feet'));?>" placeholder="Enter plot size" />

	  	<?php 
	  		if($property->market == "rent"){
	  			echo "<div>Rent term</div>";
				echo generate_form_select("rent_terms", true, array(
		  			"Month-to-Month" => 1,
		  			"2 months"       => 2,
		  			"3 months"       => 3,
		  			"6 months"       => 6,
		  			"1 year"         => 12
		  		));
			} 
		?>  	

	  	<div>Date Available</div>
	  	<input type="date" name="available" value="<?php echo escape(Input::get('available'));?>" placeholder="Enter date" />

        <div>Units&nbsp;&nbsp;(<a href="#">What is this?</a>)</div>
        <?php echo create_form_select("units", 5); ?>

	  	<div>What's special about your listing</div>
	  	<textarea name="description" cols="60" rows="4"><?php echo escape(Input::get('description'))?></textarea>

		<h4>Contact information</h4>  	 
		<div>Full Name <input type="text" name="owner" value="<?php echo escape($user->fullName());?>" placeholder="Enter name" /></div>
		<div>Email <input type="email" name="contact_email" value="<?php echo escape($user->email);?>" placeholder="Enter email" /></div>
		<div>Phone number<input type="tel" name="contact_phone" value="<?php echo escape($user->phone);?>" placeholder="Enter phone" /></div>
		
        <!-- <h4>Property features</h4>
		<div class="checkbox-group">
			<p><strong>Indoor</strong></p>
			<?php 
				// These will come from the database
				//$indoor_features = Property::features("indoor");
				//echo generate_form_checkbox("property_feature", $indoor_features);
			?>


			<p><strong>Outdoor</strong></p>
			<?php 
				// These will come from the database
				//$outdoor_features = Property::features("outdoor");
				//echo generate_form_checkbox("property_feature", $outdoor_features);
			?>		
		</div>	 -->

		<h4>Upload Photo</h4>	
    	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo Config::get('max_file_size'); ?>" />
    	<div><input type="file" name="image_upload[]"  multiple="multiple" /></div>

    	<button type="submit" class="btn btn-primary btn-block font-weight-bold">Finish listing</button>
    </form>
  
<?php include_layout_template('footer.php'); ?>
		

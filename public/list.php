<?php 
include '../private/init.php';
include PACKAGE_PATH;
include LIB_PATH.DS.'class.upload/class.upload.php';

require_login("login.php?redirect=addproperty");

use Rakit\Validation\Validator;
$validator = new Validator;

if(!Session::get('LIST_PROPERTY_ID')){
    $session->message("Please add your property");
    Redirect::prevPage();
}  

// Find the previously inserted property
$property_id = (int) Session::get('LIST_PROPERTY_ID');
$property = Property::findById($property_id);

if($property->user_id !== $user->id){
    Redirect::to("new.php");
}

if(Input::exists()){

    $validation = $validator->make($_POST + $_FILES, [
        'price'          => 'required|numeric|min:3|max:10',
        'beds'           => 'required|numeric',
        'baths'          => 'required|numeric',
        // 'rent_terms'     => 'required',        
        // 'square_feet'    => 'required|numeric',
        // 'units'          => 'numeric',
        'available'      => 'required|date',
        'description'    => 'required',
        'contact_name'   => 'required|min:3',
        'contact_email'  => 'required|email',
        'contact_phone'  => 'required|numeric',
        'photo'          => 'required|uploaded_file:0,1000K,png,jpeg'  
    ]);

    $validation->setAliases([
        // 'square_feet'      => 'Plot size',
        'available'        => 'Availability date',
        'description'      => 'Property description',
        'contact_email'    => 'Contact email',
        'contact_phone'    => 'contact phone'
    ]);

    $validation->setMessages([
        'required'           => ':attribute can not be empty',
        'available:required' => ':attribute is required',
        'bedrooms:required'  => 'Number of bedrooms cannot be blank.',
        'bathrooms:required' => 'Number of bathrooms cannot be blank.',
        'photo:required'     => ':attribute is required'
    ]);

    // run the validation method
    $validation->validate();

    if($validation->fails()) {
        // handling errors
        $errors  = $validation->errors();
        $message = implode("<br> ", $errors->firstOfAll());
    }
	else{
        // Upload the Photo(s) first
        $photo = new Photo;

        $photo->attachFile($_FILES['photo'], $property->id, false);

        if($photo->uploadSuccess()){
            // Add the property to the database

            $property->beds             = (int)    Input::get('beds');
            $property->baths            = (float)  Input::get('baths');
            $property->terms            = (string) Input::get('rent_terms');
            $property->size             = (string) "";
            $property->units            = (int)    1;
            $property->price            = (int)    Input::get('price');
            $property->negotiable       = (int)    Input::get('nego') === 'yes' ? true : false;
            $property->description      = (string) ucfirst(Input::get('description'));
            $property->contact_number   = (string) Input::get('contact_phone');
            $property->contact_email    = (string) Input::get('contact_email');
            $property->contact_name     = (string) Input::get('contact_name');
            $property->available        = (string) Input::get('available');
            $property->added            = mysql_datetime();
            $property->photo            = $photo->filename;
            $property->status           = (int)  5;            

            if($property && $property->save()){
                $session->message("Your property has been added, check to see if everthing is okay and click activate");
                Redirect::to('review.php?id='.$property->id);
            } else{
                $message = "Ooops something went wrong, try again";
            }
        }
        else{
            $message = implode("<br> ", $photo->uploadErrors());
        }

		// Add the property
        $session->message("Your property has been added, check to see if everthing is okay and click activate");
        Redirect::to('review.php?id='.$property->id);
    }
}   

$page_title = "Listing";

?>


<?php layout_template('header.php'); ?>

	<?php 
        if (empty($property->address)) {
            echo "<h3>".ucfirst($property->type)." for ".ucfirst($property->market)." ";
            echo "in ".Location::findLocationOn($property->location_id)."</h3>"; 
        }else{
            echo "<h3>For ".ucfirst($property->market)."<br>";
            echo $property->address.", ".Location::findLocationOn($property->location_id)."</h3>";
        }
	?>

    <?php echo output_message($message); ?>
    
    <form action="list.php" enctype="multipart/form-data" method="POST" accept-charset="utf-8">

  		<h4>Details and Description</h4>

  		<?php
  			if($property->market == "rent"):
                echo '<label class="control-label" for="rent_price">Rent Price</label>';
	  		else:
                echo '<label class="control-label" for="sale_price">Sale Price</label>';
            endif;    
	  	?>
	  	<input type="text" name="price" value="<?php echo escape(Input::get('price'));?>" placeholder="Enter amount" />
        <?php if($property->market == "sale"):?>
            <div style="margin: -.8rem 0 1rem 0;">
                <label><input type="checkbox" name="nego" value="yes" <?php if((isset($property) && $property->negotiable === 1) || Input::get('nego') === "yes") echo "checked";?> style="    height:20px;"> <span>Negotiable</span>
                </label>
            </div>
        <?php endif; ?>
        <?php 
            if(isset($validation) && $errors->has('price'))
            echo "<div class=\"text-danger\" style=\"margin-top:-.8rem;\">". $errors->first('price') ."</div>";
        ?>

        <label class="control-label" for="bedrooms">Bedrooms</label>
	  	<?php echo create_form_select("beds", 4); ?>
        <?php 
            if(isset($validation) && $errors->has('beds'))
            echo "<div class=\"text-danger\" style=\"margin-top:-.8rem;\">". $errors->first('beds') ."</div>";
        ?>

	  	<label class="control-label" for="bathrooms">Bathrooms</label>
	  	<?php echo create_form_select("baths", 4, "fractions"); ?>
        <?php 
            if(isset($validation) && $errors->has('baths'))
            echo "<div class=\"text-danger\" style=\"margin-top:-.8rem;\">". $errors->first('baths') ."</div>";
        ?>
	  	<!-- <label class="control-label" for="size">Square Feet</label>
	  	<input type="text" name="square_feet" value="<?php //echo escape(Input::get('square_feet'));?>" placeholder="Enter plot size" /> -->

	  	<?php if($property->market == "rent"):?>
            <label class="control-label" for="rent_term">Rent term</label>
			<?php
            	echo generate_form_select("rent_terms", true, [
		  			"Month-to-Month" => 1,
		  			"2 months"       => 2,
		  			"3 months"       => 3,
		  			"6 months"       => 6,
		  			"1 year"         => 12
		  		]);

                if(isset($validation) && $errors->has('rent_terms'))
                echo "<div class=\"text-danger\" style=\"margin-top:-.8rem;\">". $errors->first('rent_terms') ."</div>";
	      ?>  
        <?php endif ?> 

        <label class="control-label" for="date_available">Date Available</label>
	  	<input type="date" name="available" value="<?php echo escape(Input::get('available'));?>" placeholder="Enter date" />
        <?php 
            if(isset($validation) && $errors->has('available'))
            echo "<div class=\"text-danger\" style=\"margin-top:-.8rem;\">". $errors->first('available') ."</div>";
        ?>

        <!-- <div>Units&nbsp;&nbsp;(<a href="#">What is this?</a>)</div> -->
        <?php //echo create_form_select("units", 5); ?>

        <label class="control-label" for="description">What's special about your listing</label>
	  	<textarea name="description" cols="60" rows="4" id="description"><?php echo escape(Input::get('description'))?></textarea>
        <?php 
            if(isset($validation) && $errors->has('description'))
            echo "<div class=\"text-danger\" style=\"margin-top:0;\">". $errors->first('description') ."</div>";
        ?>

		<h4>Contact information</h4>  	 
		<label class="control-label" for="contact_name">Contact name</label>
        <input type="text" name="contact_name" id="contact_name" value="<?php echo escape($user->fullName());?>" placeholder="Enter name" />
        <?php 
            if(isset($validation) && $errors->has('contact_name'))
            echo "<div class=\"text-danger\" style=\"margin-top:-.8rem;\">". $errors->first('contact_name') ."</div>";
        ?>

		<label class="control-label" for="contact_email">Contact Email</label>
        <input type="email" name="contact_email" id="contact_email" value="<?php echo escape($user->email);?>" placeholder="Enter email" />
        <?php 
            if(isset($validation) && $errors->has('contact_email'))
            echo "<div class=\"text-danger\" style=\"margin-top:-.8rem;\">". $errors->first('contact_email') ."</div>";
        ?>

		<label class="control-label" for="contact_phone">Contact Phone</label>
        <input type="tel" name="contact_phone" id="contact_phone" value="<?php echo escape($user->phone);?>" placeholder="Enter phone" />
        <?php 
            if(isset($validation) && $errors->has('contact_phone'))
            echo "<div class=\"text-danger\" style=\"margin-top:-.8rem;\">". $errors->first('contact_phone') ."</div>";
        ?>

		<h4>Property features</h4>
        <div class="checkbox-group">
            <strong>Interior</strong>
            <?php 
                //These will come from the database
                $indoor_features = Property::features("indoor");
                echo generate_form_checkbox("property_feature[]", $indoor_features);

                if(isset($validation) && $errors->has('property_feature'))
                echo "<div class=\"text-danger\" style=\"margin-top:0;\">". $errors->first('property_feature') ."</div>";
            ?>

            <div style="clear:both"></div>

            <strong style="margin-top:.8rem;">Exterior</strong>
            <?php 
                //These will come from the database
                $outdoor_features = Property::features("outdoor");
                echo generate_form_checkbox("property_feature[]", $outdoor_features);

                if(isset($validation) && $errors->has('property_feature'))
                echo "<div class=\"text-danger\" style=\"margin-top:0;\">". $errors->first('property_feature') ."</div>";
            ?>      
        </div> 	
        <div style="clear:both"></div>

		<h4>Upload Photo</h4>	
    	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo Config::get('max_file_size'); ?>" />
    	<div><input type="file" name="photo" /></div>
        <?php 
            if(isset($validation) && $errors->has('photo'))
            echo "<div class=\"text-danger\" style=\"margin-top:-.5rem;\">". $errors->first('photo') ."</div>";
        ?>
        <div class="form-group">
            <label class="sr-only" for="submit"></label>
            <button type="submit" id="submit" class="btn btn-success btn-block font-weight-bold submit">Finish and Review</button>
        </div>
    </form>
  
<?php layout_template('footer.php'); ?>
		

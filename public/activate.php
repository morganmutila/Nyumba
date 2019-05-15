<?php require_once("../init.php"); ?>
<?php if (!$session->isLoggedIn()) { Redirect::to("login.php"); } ?>

<?php

$page_title = "Activate listing";

if(Input::get('property')){
	$property_id = Input::get('property');

}else{
	$session->message("Could not find that property");
	Redirect::to("properties.php");
}
	
	// Find the previously inserted property
	$property = Property::findById($property_id);

if(Input::exists()){
	if(Session::checkToken(Input::get('token'))) {
		$validate = new Validation();
        $validation = $validate->check($_POST, array(
            'price' => array(
                'required' => true,
                'number_only' => true,
                'min' => 3,
                'max' => 10
            ),
            'beds' => array(
                'required' => true,
                'number_only' => true
            ),
            'baths' => array(
                'required' => true
            ),
            'square_feet' => array(
                'required' => true,
                'number_only' => true,
            ),
            'available' => array(
                'required' => true
            ),
            'description' => array(
                'required' => true,
                'text_only' => true,
                'min' => 10
            ),
            'owner' => array(
                'required' => true,
                'min' => 3
            ),
            'contact_email' => array(
                'max' => 50,
                'valid_email' => true
            ),
            'contact_phone' => array(
                'required' => true,
                'number_only' => true,
                'min' => 10,
                'max' => 15
            ),
            'listed_by' => array(
                'required' => true
            ),
            // 'property_feature' => array(
            //     'required' => true
            // )
        ));

    	if ($validation->passed()) {
            // Add the property to the database

            $property->beds      	    = (int)    Input::get('beds');
            $property->baths     	    = (float)  Input::get('baths');
            $property->terms      	    = (string) Input::get('rent_terms');
            $property->size      		= (string) Input::get('square_feet');
            $property->price            = (string) Input::get('price');
            $property->description      = (string) ucfirst(Input::get('description'));
            $property->cphoto           = (string) Input::get('cphoto');
            $property->contact_number   = (string) Input::get('contact_phone');
            $property->contact_email    = (string) Input::get('contact_email');
            $property->owner     	    = (string) Input::get('owner');
            $property->available        = (string) Input::get('available');
            $property->status           = (int)    2;
            $property->listed_by    	= (string) Input::get('listed_by');

        	if($property->save()){
				// Add the property
                $session->message("Your property has been added and is being reviewed, it will be listed as soon as we are done");
                    Redirect::to('property.php?id='.$property->id);
            } else{
                $message = "Sorry could not list your property";
            }

        } else {
            $message = join("<br>", $validation->errors());
        }
    }
}   

?>


<?php include_layout_template('header.php'); ?>

	<?php 
		echo "<h2>FOR ".strtoupper($property->market)."<br>".$property->address.", ".Location::findLocationOn($property->location_id)."&nbsp<small><a href=\"list.php?property=$property->id\">&nbsp;&nbsp;¶</a></small></h2>";
	?>
	<?php echo output_message($message); ?>
    <form action="activate.php?property=<?php echo isset($property_id) ? $property_id: "";?>" enctype="multipart/form-data" method="POST">
  		<h4>Details and Description</h4>
  		<?php
  			echo ($property->market == "rent") ?
	  			"<div>Rent Price</div>":
		  		"<div>Sale Price</div>";
	  	?>
	  	<input type="text" name="price" value="<?php echo escape(Input::get('price'));?>" placeholder="Enter amount" />

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

	  	<div>What's special about your listing</div>
	  	<textarea name="description" cols="60" rows="4"><?php echo escape(Input::get('description'))?></textarea>

		<h4>Contact information</h4>  	 
		<div>Name <input type="text" name="owner" value="<?php echo escape($user->fullName());?>" placeholder="Enter name" /></div>
		<div>Email <input type="email" name="contact_email" value="<?php echo escape($user->email);?>" placeholder="Enter email" /></div>
		<div>Phone number<input type="tel" name="contact_phone" value="<?php echo escape($user->phone);?>" placeholder="Enter phone" /></div>
		<div>Property listed by</div>
		<div class="radio-group">
			<label><input type="radio" name="listed_by" value="1" checked="checked"/>Owner</label>
			&nbsp;&nbsp;&nbsp;
			<label><input type="radio" name="listed_by" value="2"/>Company / Broker</label>
		</div>	

		<h4>Property features</h4>
		<div class="checkbox-group">
			<p><strong>Indoor</strong></p>
			<?php 
				// These will come from the database
				$indoor_features = Property::propertyFeatures("indoor");
				echo generate_form_checkbox("property_feature", $indoor_features);
			?>


			<p><strong>Outdoor</strong></p>
			<?php 
				// These will come from the database
				$outdoor_features = Property::propertyFeatures("outdoor");
				echo generate_form_checkbox("property_feature", $outdoor_features);
			?>		
		</div>	

		<h4>Upload Photo</h4>	
    	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo Config::get('max_file_size'); ?>" />
    	<div><input type="file" name="cphoto" /></div>

    	<input type="hidden" name="token" value="<?php echo Session::generateToken(); ?>">
    	<button type="submit" class="btn btn-primary btn-block font-weight-bold">Finish listing</button>
    </form>
  
<?php include_layout_template('footer.php'); ?>
		

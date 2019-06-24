    <form action="activate.php?property=<?php echo isset($property_id) ? $property_id: "";?>" enctype="multipart/form-data" method="POST" accept-charset="utf-8">

  		<h4>Details and Description</h4>

        <div><?php echo ($property->market == "rent") ? "Rent Price":"Sale Price";?></div>

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

        <div>Units&nbsp;&nbsp;(if you have more houses of the same)</div>
        <?php echo create_form_select("units", 5); ?>

	  	<div>What's special about your listing</div>
	  	<textarea name="description" cols="60" rows="4"><?php echo escape(Input::get('description'))?></textarea>

		<h4>Contact information</h4>  	 
		<div>Full Name <input type="text" name="contact_name" value="<?php echo escape($user->fullName());?>" placeholder="Enter name" /></div>
		<div>Email <input type="email" name="contact_email" value="<?php echo escape($user->email);?>" placeholder="Enter email" /></div>
		<div>Phone number<input type="tel" name="contact_phone" value="<?php echo escape($user->phone);?>" placeholder="Enter phone" /></div>
		
        <!-- <h4>Property features</h4>
		<div class="checkbox-group">
			<p><strong>Indoor</strong></p>
			<?php 
				// These will come from the database
				// $indoor_features = Property::features("indoor");
				// echo generate_form_checkbox("property_feature", $indoor_features);
			?>

			<p><strong>Outdoor</strong></p>
			<?php 
				//These will come from the database
				// $outdoor_features = Property::features("outdoor");
				// echo generate_form_checkbox("property_feature", $outdoor_features);
			?>		
		</div>	 -->

		<h4>Upload Photo</h4>	
    	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo Config::get('max_file_size'); ?>" />
    	<div><input type="file" name="image_upload[]"  multiple="multiple" /></div>

    	<button type="submit" class="btn btn-primary btn-block font-weight-bold">Finish listing</button>
    </form>
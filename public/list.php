<?php require_once("../init.php"); ?>
<?php if (!$session->isLoggedIn()) { Redirect::to("login.php?redirect=listproperty"); } ?>

<?php

$page_title = "List property";

if(Input::get('property')){

	// Find the previously inserted property
	$property_id = Input::get('property');
	$property = Property::findById($property_id);

	if(Input::exists()){
	    if(Session::checkToken(Input::get('token'))) {
	        $validate = new Validation();
	        $validation = $validate->check($_POST, array(
	            'property_address' => array(
	                'required' => true,
	                'text_only'=> true,
	                'min' => 5
	            ),
	            'property_type' => array(
	                'required' => true
	            ),
	            'location' => array(
	                'required' => true
	            ),
	            'market_name' => array(
	                'required' => true
	            )
	        ));

	        if ($validation->passed()) {
	            $property->user_id 			= (int)    $session->user_id;
	            $property->location_id  	= (int)    Input::get('location');
	            $property->address     		= (string) Input::get('property_address');
	            $property->beds      	    = 0;
	            $property->baths     	   	= 0;
	            $property->terms      	    = "";
	            $property->size      		= 0;
	            $property->type   			= (string) Input::get('property_type');
	            $property->price            = 0.0;
	            $property->contained 		= (Input::get('self_contained') === 'yes') ? true : false;
	            $property->description      = "";
	            $property->cphoto           = "<br>";
	            $property->contact_number   = "";
	            $property->contact_email    = "";
	            $property->owner     	    = "";
	            $property->available        = "";
	            $property->reference        = (string) rand();
	            $property->status           = (int)    1;
	            $property->units            = (int)    1;
	            $property->views            = (int)    0;
	            $property->flags            = (int)    0;
	            $property->available    	= "";
	            $property->listed_by    	= "";
	            $property->market     		= (string) strtolower(Input::get('market_name'));

	        	if($property->save()){
					// Add the property
	                    Redirect::to('activate.php?property='.$property->id);
	            } else{
	                $message = "Sorry could not add your property";
	            }

	        } else {
	            $message = join("<br>", $validation->errors());
	        }
	    }
	}
}

if(Input::exists()){
    if(Session::checkToken(Input::get('token'))) {
        $validate = new Validation();
        $validation = $validate->check($_POST, array(
            'property_address' => array(
                'required' => true,
                'text_only'=> true,
                'min' => 5
            ),
            'property_type' => array(
                'required' => true
            ),
            'location' => array(
                'required' => true
            ),
            'market_name' => array(
            	'required'=> true
            ),            
            'self_contained' => array(
            	'required'=> true
            )
        ));

        if ($validation->passed()) {

    		$property = new Property();

            $property->user_id 			= (int)    $_SESSION['user_id'];
            $property->location_id  	= (int)    Input::get('location');
            $property->address     		= (string) Input::get('property_address');
            $property->beds      	    = 0;
            $property->baths     	    = 0;
            $property->terms      	    = "";
            $property->size      		= 0;
            $property->type      	    = (string) Input::get('property_type');
            $property->price            = 0.0;
            $property->contained 		= (Input::get('self_contained') === 'yes') ? true : false;
            $property->description      = "";
            $property->cphoto           = "<br>";
            $property->contact_number   = "";
            $property->contact_email    = "";
            $property->owner     	    = "";
            $property->available        = "";
            $property->reference        = (string) rand();
            $property->status           = (int)    1;
            $property->units            = (int)    1;
            $property->views            = (int)    0;
            $property->flags            = (int)    0;
            $property->available    	= "";
            $property->listed_by    	= "";
            $property->market     		= (string) strtolower(Input::get('market_name'));

        	if($property->create()){
				// Add the property
                    Redirect::to('activate.php?property='.$property->id);
            } else{
                $message = "Sorry could not add your property";
            }

        } else {
            $message = join("<br>", $validation->errors());
        }
    }
}


?>
<?php include_layout_template('header.php'); ?>

	<?php echo !isset($property) ?
		"<h2>Add a property</h2>":
		"<h2>Edit your property</h2>";
	?>

	<h4>Provide your property's info</h4>
	<?php echo output_message($message); ?>
  	<form action="list.php?property=<?php echo isset($property_id) ? $property_id: "";?>"  method="POST">
	  	  	<div>Property Name / Address</div>
	  		<input type="text" name="property_address" value="<?php echo isset($property) ? $property->address : Input::get('address');?>" placeholder="Address or name"/>

	  		<div>Property Type</div>
  			<?php

  			$property_types = array(
	  			"House" 			        => "House",
	  			"Flat"				    	=> "Flat",
	  			"Apartment"				    => "Apartment",
	  			"Apartment(semi-detached)"  => "Apartment(semi-detached)",
	  			"Town House"		    	=> "Town House"
	  		);

	        $select_property_type = "<select name=\"property_type\">";
	            $select_property_type .= "<option value=\"\">Please select</option>";
	            foreach ($property_types as $type => $value) {
	                $select_property_type .= "<option value=\"$value\" ";
	                    if((isset($property) && $property->type == $value) || Input::get('property_type') == $value){
	                        $select_property_type .= "selected=\"selected\"";
	                    }
	                $select_property_type .= ">".$type."</option>";
	            }
	        $select_property_type .= "</select>";
	        echo $select_property_type;
  			?>


	  		<div>Location</div>
  			<?php
		        $select_location = "<select name=\"location\">";
		            $select_location .= "<option value=\"\">Please select</option>";
		            foreach (Location::AllLocations() as $key => $value) {
		                $select_location .= "<option value=\"$value\" ";
		                    if((isset($property) && $property->location_id == $value) || Input::get('location') == $value){
		                        $select_location .= "selected=\"selected\"";
		                    }
		                $select_location .= ">".$key."</option>";
		            }
		        $select_location .= "</select>";
		        echo $select_location;
			?>

			<div>Market</div>
			<div class="radio-group">
	  			<label><input type="radio" name="market_name" value="rent" checked="checked" <?php if((isset($property) && $property->market == "rent") || Input::get('market_name') == "rent"){echo "checked=\"checked\"";} ?> style="margin-left: 0;" />For Rent&nbsp;&nbsp;</label>
	  			<label><input type="radio" name="market_name" value="sale" <?php if((isset($property) && $property->market == "sale") || Input::get('market_name') == "sale"){echo "checked=\"checked\"";} ?>  />For Sale</label>
	  		</div>
			<div class="checkbox-group">
			<p><label><input type="checkbox" name="self_contained" value="yes" <?php if((isset($property) && $property->contained === "1") || Input::get('self_contained') === "yes"){echo "checked=\"checked\"";} ?>> This listing is self contained</label></p>
			</div>	
	    <p><input type="hidden" name="token" value="<?php echo Session::generateToken(); ?>">
	    <button type="submit" class="btn btn-primary btn-block font-weight-bold">Add property</button></p>
  	</form>


<?php include_layout_template('footer.php'); ?>

<?php 
require '../init.php';
require PACKAGE_PATH;
if (!$session->isLoggedIn()) { Redirect::to("login.php?redirect=listproperty"); } 


$page_title = "List property";

use Rakit\Validation\Validator;
$validator = new Validator;


if(Input::get('property')) {
	$property_id = (int) Input::get('property');
	$property = Property::findById($property_id);
	if($property->user_id !== $user->id) {
        //Prevents insertions of Property ID in the url string
		Redirect::to("index.php");
	}
}else{
	$property = new Property();
}	

if(Input::exists()){
    if(Session::checkToken(Input::get('token'))) {

        $validation = $validator->make($_POST, [
            'property_type'     => 'required',
            'location'          => 'required',
            'market_name'       => 'required'
        ]);

        $validation->setAliases([
            'property_address'  => 'Property address',
            'property_type'     => 'Property type',
            'market_name'       => 'Market type'
        ]);

        $validation->setMessages([
            'required'      => ':attribute can not be empty'
        ]);

        // run the validation method
        $validation->validate();

        if($validation->fails()) {
            // handling errors
            $errors  = $validation->errors();
            $message = implode(", ", $errors->firstOfAll());
        }
        else {

            $property->user_id 			= (int)    $session->user_id;
            $property->location_id  	= (int)    Input::get('location');
            $property->address     		= (string) Input::get('property_address');
            $property->beds      	    = 0;
            $property->baths     	    = 0;
            $property->terms      	    = "";
            $property->size      		= 0;
            $property->type      	    = (string) Input::get('property_type');
            $property->price            = 0;
            $property->price_old        = 0;
            $property->negotiabe 		= 0;
            $property->description      = "";
            $property->cphoto           = "";
            $property->contact_number   = $user->phone;
            $property->contact_email    = $user->email;
            $property->owner     	    = $user->fullName();
            $property->available        = "";
            $property->reference        = (string) rand();
            $property->status           = (int)    1;
            $property->units            = (int)    1;
            $property->views            = (int)    0;
            $property->flags            = (int)    0;
            $property->available    	= "";
            $property->listed_by        = (int)    1;
            $property->market     		= (string) strtolower(Input::get('market_name'));

        	if($property && $property->save()){
				// Add the property
                Redirect::to('activate.php?property='.$property->id);
            } else{
                $message = "Oops! could not add your property, something went wrong, please try again";
            }

        }
    }
}


?>
<?php include_layout_template('header.php'); ?>

	<?php echo !isset($property_id) ?
		"<h2>Add a property</h2>":
		"<h2>Edit your property</h2>";
	?>

	<!-- <h4>Provide your property info</h4> -->
	<?php echo output_message($message, "danger"); ?>
  	<form action="list.php?property=<?php echo isset($property_id) ? $property_id: "";?>"  method="POST" accept-charset="utf-8">
	  		<div>Property Type</div>
  			<?php

  			$property_types = array(
	  			"House" 			        => "House",
	  			"Flat"				    	=> "Flat",
	  			"Apartment"				    => "Apartment",
	  			"Apartment(semi-detached)"  => "Apartment(semi-detached)",
	  			"Townhouse"	      	    	=> "Townhouse",
                "Condo"                     => "Condo"
	  		);

	        $select_property_type = "<select name=\"property_type\">";
	            $select_property_type .= "<option value=\"\">Please select --</option>";
	            foreach ($property_types as $type => $value) {
	                $select_property_type .= "<option value=\"$value\" ";
	                    if((isset($property) && $property->type == $value) || Input::get('property_type') == $value){
	                        $select_property_type .= "selected";
	                    }
	                $select_property_type .= ">".$type."</option>";
	            }
	        $select_property_type .= "</select>";
	        echo $select_property_type;
  			?>


	  		<div>Location</div>
  			<?php
		        $select_location = "<select name=\"location\">";
		            $select_location .= "<option value=\"\">Please select --</option>";
		            foreach (Location::AllLocations() as $key => $value) {
		                $select_location .= "<option value=\"$value\" ";
		                    if((isset($property) && $property->location_id == $value) || Input::get('location') == $value || $session->location == $value){
		                        $select_location .= "selected";
		                    }
		                $select_location .= ">".$key."</option>";
		            }
		        $select_location .= "</select>";
		        echo $select_location;
			?>
            
            <div>Property Name / Address</div>
            <input type="text" name="property_address" value="<?php echo isset($property) ? $property->address : Input::get('address');?>" placeholder="Address or name"/>

			<div>Market</div>
			<div class="radio-group">
	  			<label><input type="radio" name="market_name" value="rent" checked="checked" <?php if((isset($property) && $property->market == "rent") || Input::get('market_name') == "rent"){echo "checked";} ?> style="margin-left: 0;" />For Rent&nbsp;&nbsp;</label>
	  			<label><input type="radio" name="market_name" value="sale" <?php if((isset($property) && $property->market == "sale") || Input::get('market_name') == "sale"){echo "checked";} ?>  />For Sale</label>
	  		</div>	<br>
	    <p><input type="hidden" name="token" value="<?php echo Session::generateToken(); ?>">
	    <button type="submit" class="btn btn-primary btn-block font-weight-bold">Add property</button></p>
  	</form>


<?php include_layout_template('footer.php'); ?>

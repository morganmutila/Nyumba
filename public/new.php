<?php 
include '../private/init.php';
include PACKAGE_PATH;
require_login("login.php?redirect=addproperty");

use Rakit\Validation\Validator;
$validator = new Validator;

$sql = "SELECT * FROM property WHERE status = ? AND user_id = ? LIMIT 1";

$pending_listing = Property::findBySql($sql, [1, $user->id]);

if(count($pending_listing) > 0){
    $session->message("You have an unfinished listing, complete the listing to continue");
    Redirect::to("properties.php");   
}

if(is_post_request()){

    $validation = $validator->make($_POST, [
        'property_type'     => 'required',
        'location'          => 'required',
        'market_name'       => 'required',
        // 'property_address'  => 'min:3'
    ]);

    $validation->setAliases([
        // 'property_address'  => 'Property address',
        'property_type'     => 'Property type',
        'market_name'       => 'Market type'
    ]);

    $validation->setMessages([
        'property_type:required' => 'Please tell us the type of property you are listing.',
        'location:required'      => 'Specify where your property is located.',
        'market_name:required'   => 'Tell us the type of market you are listing.',
        // 'property_address:min'   => 'The minimum characters in the :attribute or name is 3'
    ]);

    // run the validation method
    $validation->validate();

    if($validation->fails()) {
        // handling errors
        $errors  = $validation->errors();
    }
    else{
        $property = new Property;
        $property->user_id 			= current_user_id();
        $property->location_id  	= (int)    Input::get('location');
        $property->address     		= "";
        $property->beds      	    = 0;
        $property->baths     	    = 0;
        $property->terms      	    = "";
        $property->size      		= 0;
        $property->type      	    = (string) Input::get('property_type');
        $property->price            = 0;
        $property->price_old        = 0;
        $property->negotiabe 		= 0;
        $property->description      = "";
        $property->photo            = "";
        $property->contact_number   = $user->phone;
        $property->contact_email    = $user->email;
        $property->contact_name     = $user->fullName();
        $property->available        = "";
        $property->reference        = (string) rand();
        $property->status           = (int)    1;
        $property->units            = (int)    1;
        $property->views            = (int)    0;
        $property->flags            = (int)    0;
        $property->available    	= "";
        $property->listed_by        = (int)    1;
        $property->market     		= (string) strtolower(Input::get('market_name'));
        $property->added            = "";
        $property->status           = (int)    1;
    	
        if($property->create()){
            // Add the property and re-direct            
            Session::put('LIST_PROPERTY_ID', $property->id);
            Redirect::to("list.php");
        }
        else{
            $message = "Oops! could not add your property, something went wrong, please try again";
        }
    }    
}

$page_title = "Add your property";
?>
<?php layout_template('header.php'); ?>

	<h2>Add your property</h2>

    <?php echo output_message($message); ?>

  	<form action="<?php echo escape($_SERVER['PHP_SELF']);?>" method="POST" accept-charset="utf-8">

  		<label class="control-label" for="property_type">Property Type</label>
		<?php

            // Property Type array
            $property_types = [
                "House"                     => "House",
                "Flat"                      => "Flat",
                "Apartment"                 => "Apartment",
                "Semi-detached house"       => "Semi-detached house",
                "Townhouse"                 => "Townhouse"
            ];

            $select_property_type = "<select name=\"property_type\" id=\"property_type\">";
                $select_property_type .= "<option value=\"\">Please select --</option>";
                foreach ($property_types as $type => $value) {
                    $select_property_type .= "<option value=\"$value\" ";
                        if(Input::get('property_type') == $value){
                            $select_property_type .= "selected";
                        }
                    $select_property_type .= ">".$type."</option>";
                }
            $select_property_type .= "</select>";
            echo $select_property_type;
		?>
        <?php 
            if(isset($validation) && $errors->has('property_type'))
            echo "<div class=\"text-danger\" style=\"margin-top:-.8rem;\">". $errors->first('property_type') ."</div>";
        ?>

        <label class="control-label" for="location">Location</label>
		<?php
	        $select_location = "<select name=\"location\" id=\"location\">";
	            $select_location .= "<option value=\"\">Please select --</option>";
	            foreach (Location::AllLocations() as $key => $value) {
	                $select_location .= "<option value=\"$value\" ";
	                    if(Input::get('location') == $value || $session->location == $value){
	                        $select_location .= "selected";
	                    }
	                $select_location .= ">".$key."</option>";
	            }
	        $select_location .= "</select>";
	        echo $select_location;
		?>
        <?php 
            if(isset($validation) && $errors->has('location'))
            echo "<div class=\"text-danger\" style=\"margin-top:-.8rem;\">". $errors->first('location') ."</div>";
        ?>
        
        <div id="_rent" class="radioradio">
            <label class="control-label" for="rent">
                <input type="radio" name="market_name" id="rent" value="rent" checkbox-inline="inline" <?php if(Input::get('market_name') == "rent") echo "checked";?> > For Rent
            </label>
            &nbsp;&nbsp;&nbsp;
            <label class="control-label" for="sale">
                <input type="radio" name="market_name" id="sale" value="sale" checkbox-inline="inline" <?php if(Input::get('market_name') == "sale") echo "checked";?> > For Sale
            </label>
        </div>
        <?php 
            if(isset($validation) && $errors->has('market_name'))
            echo "<div class=\"text-danger\" style=\"margin-top:-.3rem;\">". $errors->first('market_name') ."</div>";
        ?>

        <div class="form-group">
            <label class="sr-only" for="submit"></label>
            <button type="submit" id="submit" class="btn btn-success btn-block font-weight-bold submit">Add property</button>
        </div>
  	</form>

<?php layout_template('footer.php'); ?>

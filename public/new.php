<?php 
include '../private/init.php';
include PACKAGE_PATH;
include LIB_PATH .'/class.upload/class.upload.php';

require_login("login.php?redirect=addproperty");


use Rakit\Validation\Validator;
$validator = new Validator;

// Find if there was a prevuiously incompleted listing
$sql = "SELECT * FROM property WHERE status = ? AND user_id = ? LIMIT 1";
$property = Property::findFirst($sql, [1, $user->id]);

if(!$property){
    if(Input::exists()){
        $validation = $validator->make($_POST, [
            'property_type'     => 'required',
            'location'          => 'required',
            'market_name'       => 'required',
            'property_address'  => 'min:3'
        ]);

        $validation->setAliases([
            'property_address'  => 'Property address',
            'property_type'     => 'Property type',
            'market_name'       => 'Market type'
        ]);

        $validation->setMessages([
            'property_type:required' => 'Please tell us the type of property you are listing.',
            'location:required'      => 'Specify where your property is located.',
            'market_name:required'   => 'Tell us the type of market you are listing.',
            'property_address:min'   => 'The minimum characters in the :attribute or name is 3'
        ]);

        // run the validation method
        $validation->validate();

        if($validation->fails()) {
            // handling errors
            $errors  = $validation->errors();
        }
        else{
            $property = new Property;
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
                Redirect::to("new.php?details=true");
            }
            else{
                $message = "Oops! could not add your property, something went wrong, please try again";
            }
        }    
    }    
}

elseif($property && Input::get('details') == 'true'){
    $session->message("You have an unfinished listing, complete the listing to continue");
    Session::put('LIST_PROPERTY_ID', $property->id);

    if($property->user_id !== $user->id){
        Session::delete('LIST_PROPERTY_ID');
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
        }
    }  
}

$page_title = "Add your property";

?>
<?php layout_template('header.php'); ?>

	<?php if(Input::get('details') == 'true'): ?>

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
        
        <form action="new.php?details=true" enctype="multipart/form-data" method="POST" accept-charset="utf-8">

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

    <?php else: ?>
    
        <h2>Add your property</h2>

        <?php echo output_message($message); ?>

        <form action="<?php echo escape($_SERVER['PHP_SELF']);?>" method="POST" accept-charset="utf-8">

            <label class="control-label" for="property_address">Property Name / Address</label>
            <input type="text" id="property_address" name="property_address" value="<?php echo Input::get('address');?>" placeholder="Address or name"/>
            <?php 
                if(isset($validation) && $errors->has('property_address'))
                echo "<div class=\"text-danger\" style=\"margin-top:-.8rem;\">". $errors->first('property_address') ."</div>";
            ?>

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

    <?php endif; ?>    

<?php layout_template('footer.php'); ?>

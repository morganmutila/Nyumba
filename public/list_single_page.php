<?php 
include '../init.php';
include PACKAGE_PATH;
$session->comfirm_logged_in("login.php?redirect=addproperty");

if(!Input::get('property')){
    $session->message("Could not find property");
    Redirect::to("properties.php");
}  

//Set a page name variable
$page_name = escape("list.php");

// Find the previously inserted property
$property_id =  (int) Input::get('property');
$property = Property::findById($property_id);

// Always get the action
$action = Input::get('action') ? (string) Input::get('action') : "description";

use Rakit\Validation\Validator;
$validator = new Validator;

// Process form: Property_description
if(isset($_POST['property_description'])){

    $validation = $validator->make($_POST, [
        'price'          => 'required|numeric|min:3|max:10',
        'beds'           => 'required|numeric',
        'baths'          => 'required|numeric',
        'square_feet'    => 'required|numeric',
        'available'      => 'required|date',
        'description'    => 'required'
    ]);

    $validation->setAliases([
        'square_feet'      => 'Plot size',
        'available'        => 'Date available',
        'description'      => 'Property description'
    ]);

    $validation->setMessages([
        'required'              => ':attribute can not be empty',
        'available:required'    => ':attribute is required'
    ]);

    // run the validation method
    $validation->validate();

    if($validation->fails()){
        // handling errors
        $errors  = $validation->errors();
        $message = pre($errors->firstOfAll());
    }
	else{
        // Add the property to the database

        $property->beds      	    = (int)    Input::get('beds');
        $property->baths     	    = (float)  Input::get('baths');
        $property->terms      	    = (string) Input::get('rent_terms');
        $property->size      		= (string) Input::get('square_feet');
        $property->price            = (int)    Input::get('price');
        $property->negotiable       = (int)    (Input::get('nego') === 'yes') ? true : false;
        $property->description      = (string) ucfirst(Input::get('description'));
        $property->status           = (int)    2;            

    	if($property && $property->save()){
			//Redirect with a message
            $build_url = rawurlencode($page_name)."?property=".urlencode($property->id)."&action=".urlencode('contact');
            $session->message("(2) Your property description have been added, continue...");
            Redirect::to($build_url);
        } else{
            $message = "Ooops something went wrong, try again";
        }  
    }
}  

// Process form: property_contact 
if(isset($_POST['property_contact'])){

    $validation = $validator->make($_POST, [
        'contact_name'   => 'required|min:3',
        'contact_email'  => 'required|email',
        'contact_phone'  => 'required|numeric'
    ]);

    $validation->setAliases([
        'contact_name'     => 'Contact name',
        'contact_email'    => 'Contact email',
        'contact_phone'    => 'Contact phone'
    ]);

    // run the validation method
    $validation->validate();

    if($validation->fails()){
        // handling errors
        $errors  = $validation->errors();
        $message = pre($errors->firstOfAll());
    }
    else{
        // Add the property to the database
        $property->contact_number   = (string) Input::get('contact_phone');
        $property->contact_email    = (string) Input::get('contact_email');
        $property->contact_name     = (string) Input::get('contact_name');
        $property->available        = (string) Input::get('available');
        $property->status           = (int)    3;            

        if($property && $property->save()){
            //Redirect with a message
            $build_url = rawurlencode($page_name)."?property=".urlencode($property->id)."&action=".urlencode('features');
            $session->message("(3) Your contact details have been added, continue...");
            Redirect::to($build_url);
        } else{
            $message = "Ooops something went wrong, try again";
        }    
    }
}  

// Process form: property_features
if(isset($_POST['property_features'])){

    //Get ready to save the amenities
    $amenities = new Amenities;

    if($amenities->add(Input::get('property_feature'), $property->id)){
        $property->status = (int) 4;
        if($property->save()){
            //Redirect with a message
            $build_url = rawurlencode($page_name)."?property=".urlencode($property->id)."&action=".urlencode('photos');
            $session->message("(4) Your Property features have been added, continue...");
            Redirect::to($build_url);
        }
    }
    else{
        $message = "Ooops something went wrong, try again";
    }
}  

// Process form: property_uploads
if(isset($_POST['property_uploads'])){

    $validation = $validator->make($_FILES, [
        'image_upload' => 'required|uploaded_file:10k,20000M,png,jpeg'  
    ]);

    $validation->setAliases([
        'image_upload'     => 'property photo'
    ]);

    $validation->setMessages([
        'image_upload:required' => 'You need to upload a :attribute to continue'
    ]);

    // run the validation method
    $validation->validate();

    if($validation->fails()){
        // handling errors
        $errors  = $validation->errors();
        $message = pre($errors->firstOfAll());
    }
    else{
        // Upload the Photo(s) first
        $photo = new Photo();

        $photo->attachFile($_FILES['image_upload'], $property->id, false);

        if($photo->uploadSuccess()){
            // Add the property to the database
            $property->photo   = $photo->filename;
            $property->status  = (int)  5;            

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

$page_title = "Activate listing";
?>

<?php include_layout_template('header.php'); ?>

	<h2>
        <?php 
        // if (empty($property->address)):
        //     echo strtoupper($property->type)." FOR ".strtoupper($property->market)."<br>";
        //     echo "in ".Location::findLocationOn($property->location_id);
        // else :
        //     echo "FOR ".strtoupper($property->market)."<br>";
        //     echo $property->address.", ".Location::findLocationOn($property->location_id);
        // endif; ?>        
    </h2>
    
    <?php echo output_message($message, "danger"); ?>    

    <?php if($action == "description"): ?>
        <h3>Details and Description</h3>
        <form action="list.php?property=<?php echo $property->id;?>&action=<?php echo Input::get('action');?>" method="POST" accept-charset="utf-8">
            <div><?php echo ($property->market == "rent") ? "Rent Price":"Sale Price";?></div>
            <input type="text" name="price" value="<?php echo escape(Input::get('price'));?>" placeholder="Enter amount" />

            <?php if($property->market == "sale"):?>
                <p style="margin-top: -25px;">
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
                    echo generate_form_select("rent_terms", false, array(
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
            <button type="submit" name="property_description" class="btn btn-primary btn-block font-weight-bold">Continue</button>
        </form>
    <?php endif ?>    

    <?php if($action == "contact"): ?>
        <h3>Contact information</h3> 
        <form action="list.php?property=<?php echo $property->id;?>&action=<?php echo Input::get('action');?>" method="POST" accept-charset="utf-8">    
            <div>Full Name <input type="text" name="contact_name" value="<?php echo escape($user->fullName());?>" placeholder="Enter name" /></div>
            <div>Email <input type="email" name="contact_email" value="<?php echo escape($user->email);?>" placeholder="Enter email" /></div>
            <div>Phone number<input type="tel" name="contact_phone" value="<?php echo escape($user->phone);?>" placeholder="Enter phone" /></div>
            <button type="submit" name="property_contact" class="btn btn-primary btn-block font-weight-bold">Continue</button>
        </form>
    <?php endif ?> 

    <?php if($action == "features"): ?>
        <h3>Property features</h3>
        <form action="list.php?property=<?php echo $property->id;?>&action=<?php echo Input::get('action');?>" method="POST" accept-charset="utf-8">
            <div class="checkbox-group">
                <strong>Indoor</strong>
                <?php 
                    //These will come from the database
                    $indoor_features = Property::features("indoor");
                    echo generate_form_checkbox("property_feature[]", $indoor_features);
                ?>
                <div style="clear: both"></div>
                <strong style="margin-top: 1rem">Outdoor</strong>
                <?php 
                    //These will come from the database
                    $outdoor_features = Property::features("outdoor");
                    echo generate_form_checkbox("property_feature[]", $outdoor_features);
                ?>      
            </div> 
            <button type="submit" name="property_features" class="btn btn-primary btn-block font-weight-bold">Continue</button></div>
        </form>
    <?php endif ?> 

    <?php if($action == "photos"): ?>
        <h3>Upload Photo</h3> 
        <form action="list.php?property=<?php echo $property->id;?>&action=<?php echo Input::get('action');?>" enctype="multipart/form-data" method="POST" accept-charset="utf-8">
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo Config::get('max_file_size'); ?>" />
            <input type="file" name="image_upload"/>
            <button type="submit" name="property_uploads" class="btn btn-primary btn-block font-weight-bold">Finish & Review</button>
        </form>
    <?php endif ?> 

<?php include_layout_template('footer.php'); ?>
		

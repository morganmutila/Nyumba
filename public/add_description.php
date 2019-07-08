<?php 
require '../init.php';
require PACKAGE_PATH;
require LIB_PATH.DS.'class.upload'.DS.'class.upload.php';
if(!$session->isLoggedIn()){ Redirect::to("login.php?redirect=addproperty");} 

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

<?php include_layout_template('footer.php'); ?>
		

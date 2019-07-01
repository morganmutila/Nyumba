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


// Process form: property_features
if(isset($_POST['property_features'])){

    //Get ready to save the amenities
    $amenities = new Amenities;

    if($amenities->add(Input::get('property_feature'), $property->id)){
        $property->status = (int) 4;
        if($property && $property->save()){
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

    <h3>Property features</h3>
    <form action="list.php?property=<?php echo $property->id;?>&action=<?php echo Input::get('action');?>" method="POST" accept-charset="utf-8">
        <div class="checkbox-group">
            <strong>Indoor</strong>
            <?php 
                //These will come from the database
                $indoor_features = Property::features("indoor");
                echo generate_form_checkbox("property_feature[]", $indoor_features);
            ?>
            <strong style="margin-top: 2rem">Outdoor</strong>
            <?php 
                //These will come from the database
                $outdoor_features = Property::features("outdoor");
                echo generate_form_checkbox("property_feature[]", $outdoor_features);
            ?>      
        </div>  
        <div>
        <button type="submit" name="property_features" class="btn btn-primary btn-block font-weight-bold">Continue</button></div>
    </form>

<?php include_layout_template('footer.php'); ?>
		

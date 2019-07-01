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

    <h3>Contact information</h3> 
    <form action="list.php?property=<?php echo $property->id;?>&action=<?php echo Input::get('action');?>" method="POST" accept-charset="utf-8">    
        <div>Full Name <input type="text" name="contact_name" value="<?php echo escape($user->fullName());?>" placeholder="Enter name" /></div>
        <div>Email <input type="email" name="contact_email" value="<?php echo escape($user->email);?>" placeholder="Enter email" /></div>
        <div>Phone number<input type="tel" name="contact_phone" value="<?php echo escape($user->phone);?>" placeholder="Enter phone" /></div>
        <button type="submit" name="property_contact" class="btn btn-primary btn-block font-weight-bold">Continue</button>
    </form>

<?php include_layout_template('footer.php'); ?>
		

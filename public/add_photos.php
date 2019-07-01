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

    <h3>Upload Photo</h3> 
    <form action="list.php?property=<?php echo $property->id;?>&action=<?php echo Input::get('action');?>" enctype="multipart/form-data" method="POST" accept-charset="utf-8">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo Config::get('max_file_size'); ?>" />
        <input type="file" name="image_upload"/>
        <button type="submit" name="property_uploads" class="btn btn-primary btn-block font-weight-bold">Finish & Review</button>
    </form>

<?php include_layout_template('footer.php'); ?>
		

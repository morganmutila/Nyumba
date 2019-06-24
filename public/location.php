<?php
require "../init.php";
require PACKAGE_PATH;

use Rakit\Validation\Validator;
$validator = new Validator;

	
if(Input::exists()){
    $validation = $validator->make($_POST, [
        'location'  => 'required'
    ]);


    $validation->setMessages([
        'required'  => 'Select a :attribute to view listings based on location'
    ]);

    // run the validation method
    $validation->validate();

    if($validation->fails()) {
        // handling errors
        $errors  = $validation->errors();
        $message = implode(", ", $errors->firstOfAll());
    }
    else{      
		// Get the locaton in the form
		$form_location =  (int)Input::get('location');     
	
		if($session->isLoggedIn()){
			$user->location_id  = $form_location;
        	if($user->save()){
				//Add the location in a session
				Session::put('LOCATION', $user->location_id);
				$session->message("We have saved ".$user->location()." as your default location for listed houses");
                Redirect::to("index.php");
            } else{
                $message = $user->location()." is still your default location";
            }
        }
        else{
        	if($form_location === $session->location){
                $message = Location::findLocationOn($form_location)."  is still your default location";
            }
            else{
				Session::put('LOCATION', $form_location);
				$session->message("We have saved ".Location::findLocationOn($form_location)." as your default location for listed property");
	            Redirect::to("index.php");
	        }
        }
    } 
}

$page_title = "Add your location";
?>
<?php include_layout_template('header.php'); ?>

	<h2><?php echo (isset($session->location) || isset($user->location_id)) ? "Change location":"Add your location";?></h2>
	
	<?php echo flash("joined", "success"); ?>

	<p>Select your location here, we will display property based on your location, you can always change your location</p>

	<?php echo output_message($message); ?>

  	<form action="location.php" method="POST">
		<?php 
	        $select_location  = "<select name=\"location\">";
	        $select_location .= 	"<option value=\"\">Please select --</option>";
	        foreach (Location::AllLocations() as $key => $value) {
		        $select_location .= 	"<option value=\"$value\" ";
	                   					 	if(($user && $user->location_id == $value) || (int)Input::get('location') == $value || (Session::exists('LOCATION') && $session->location == $value))
		        $select_location .= 		"selected=\"selected\"";
		                    
		        $select_location .= 	">".$key."</option>";
	        }            
	        $select_location .= "</select>";

	        // Display the select field
	        echo $select_location;
		?>

	    <p>
	    <a href="index.php"><button type="button" class="btn btn-white btn-block font-weight-bold">Cancel</button></a>
	    &nbsp;
	    <button type="submit" class="btn btn-primary btn-block font-weight-bold">Save location</button></p>
  	</form>
  

<?php include_layout_template('footer.php'); ?>
		

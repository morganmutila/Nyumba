<?php require_once("../init.php"); 

$page_title = "Add your location";
	
if(Input::exists()){
    if(Session::checkToken(Input::get('token'))) {
        $validate = new Validation();
        $validation = $validate->check($_POST, array('location' => array('required' => false)));

        if ($validation->passed()) {      
        	// Get the locaton in the form
        	$form_location =  (int)Input::get('location');     
        	
			if($session->isLoggedIn()){
				$user->location_id  = $form_location;
	        	if($user->save()){
					//Add the location in a session
					Session::put('LOCATION', $user->location_id);
					$session->message("We have saved ".$user->location()." as your default location for listed houses");
	                Redirect::to("index.php?location={$user->location_id}");
	            } else{
	                $message = $user->location()." is still your default location";
	            }
	        }else{
	        	if($form_location === $session->location){
	                $message = Location::findLocationOn($form_location)."  is still your default location";
	            } else{
					Session::put('LOCATION', $form_location);
					$session->message("We have saved ".Location::findLocationOn($form_location)." as your default location for listed property");
		            Redirect::to("index.php?location=".$session->location);
		        }
	        }
	            

        } else {
            $message = implode("<br>", $validation->errors());
        }
    } 
}


?>
<?php include_layout_template('header.php'); ?>

	<?php echo (isset($session->location) || isset($user->location_id)) ?		
		"<h2>Change location</h2>":
		"<h2>Add your location</h2>";
	?>
	
	<?php echo flash("joined", "success"); ?>

	<p>Select your location here, we will display property based on your location, you can always change your location</p>

	<?php echo output_message($message); ?>

  	<form action="location.php" method="POST">
  		<div>Location</div>
		<?php 
	        $select_location = "<select name=\"location\">";
	            $select_location .= "<option value=\"\">Please select --</option>";
	            foreach (Location::AllLocations() as $key => $value) {
	                $select_location .= "<option value=\"$value\" ";
	                    if(($user && $user->location_id == $value) || (int)Input::get('location') == $value || (Session::exists('LOCATION') && $session->location == $value)){ 
	                        $select_location .= "selected=\"selected\"";
	                    }
	                $select_location .= ">".$key."</option>";
	            }            
	        $select_location .= "</select>";
	        echo $select_location;
		?>

	    <p><input type="hidden" name="token" value="<?php echo Session::generateToken(); ?>">
	    <button type="submit" class="btn btn-primary btn-block font-weight-bold">Save</button></p>
  	</form>
  

<?php include_layout_template('footer.php'); ?>
		

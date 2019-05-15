<?php require_once("../init.php"); 

$page_title = "Add your location";
	
if(Input::exists()){
    if(Session::checkToken(Input::get('token'))) {
        $validate = new Validation();
        $validation = $validate->check($_POST, array('location' => array('required' => true)));

        if ($validation->passed()) {            
        	
			if($session->isLoggedIn()){
				$user->location_id  = (int) Input::get('location');
	        	if($user->save()){
					//Add the location in a session
					Session::put('location', $user->location_id);
					$session->message("We have saved ".$user->location($user->location_id)." as your default location for property listing");
	                Redirect::to("index.php?location={$user->location_id}");
	            } else{
	                $message = $user->location($user->location_id." is still your default location");
	            }
	        }else{
				Session::put('location', (int)Input::get('location'));
	            Redirect::to("index.php?location=".Session::get('location'));
	        }
	            

        } else {
            $message = join("<br>", $validation->errors());
        }
    } 
}


?>
<?php include_layout_template('header.php'); ?>

	<h2>Add your location</h2>
	<p>We will display property based on your location, you can always change this</p>

	<?php echo output_message($message); ?>

  	<form action="location.php" method="POST">
  		<div>Location</div>
		<?php 
	        $select_location = "<select name=\"location\">";
	            $select_location .= "<option value=\"\">Please select</option>";
	            foreach (Location::AllLocations() as $key => $value) {
	                $select_location .= "<option value=\"$value\" ";
	                    if((isset($user) && $user->location_id == $value) || Input::get('location') == $value || (Session::exists('location') && Session::get('location') == $value)){ 
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
		

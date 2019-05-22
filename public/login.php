<?php require '../init.php';
if($session->isLoggedIn()){ Redirect::to("index.php");}

$page_title = "Login - Nyumba Yanga";

// Get ready to store and output form errors
$validation = new Validation();


if(Input::exists()){
    if(Session::checkToken(Input::get('token'))){
        
        $validation->check($_POST, array(
            'username'  => array('required' => 'true', 'min' => 3),
            'password'  => array('required' => 'true')
        ));

        if($validation->passed()){

            // Check the database to see if username / password exists
            $username = $email = $phone = Input::get('username');
            $password =  Input::get('password');

            $found_user = User::authenticate($username, $password, $email, $phone);

            if($found_user){
                $session->login($found_user);
                Redirect::to("index.php");
            } else {
                $message = "Log in failed, username or password does not match any account";
            }

        } else {
            $message = get_form_errors($validation->errors());
        }
    }
}

?>

<?php include_layout_template('header.php'); ?>

<?php if(Input::get('redirect') == "listproperty"){?>
    <h2 class="text-center mb-4 font-weight-bold">Log in or sign up to list your property</h2>
<?php } 
elseif(Input::get('redirect') == "savedproperty"){?>
    <h2 class="text-center mb-4 font-weight-bold">Log in or sign up to save a listing</h2>  
<?php }
else {?>
    <h2 class="text-center mb-4 font-weight-bold">Log in to Nyumba yanga</h2>
<?php } ?>

<?php echo output_message($message, "danger"); ?>

<form action="login.php" method="post" autocomplete="off">    
    <div class="form-group mb-3">
        <label for="username" class="d-none">Username</label>
        <input type="text" name="username" class="form-control" placeholder="Email or Phone" value="<?php echo escape(Input::get('username'))?>" <?php if(array_key_exists('username', $validation->errors())){echo "style=\"border: 1px solid red;\"";}?>/>        
    </div>
    <div class="form-group">
        <label for="password" class="d-none">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Password" <?php if(array_key_exists('password', $validation->errors())){echo "style=\"border: 1px solid red;\"";}?>/> 
    </div>
    <p class="form-text"><a href="#" class="small text-muted">Forgot password?</a></p>
   
    <div class="form-group text-center mb-4">    
        <input type="hidden" name="token" value="<?php echo Session::generateToken(); ?>"/>
        <button type="submit" class="btn btn-primary btn-block font-weight-bold">Log in</button>
    </div>  
    <p class="my-3 text-center">New to Nyumba Yanga?&nbsp;<a href="signup.php" class="small text-muted">Join now</a></p>                      
</form>

<?php include_layout_template('footer.php'); ?>
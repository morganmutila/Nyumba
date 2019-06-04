<?php 
require '../init.php';
require PACKAGE_PATH;

if($session->isLoggedIn()){ Redirect::to("index.php");}

$page_title = "Login - Nyumba Yanga";

use Rakit\Validation\Validator;
$validator = new Validator;

// Get ready to store and output form errors
// $validation = new Validation();

if(Input::exists()){
    if(Session::checkToken(Input::get('token'))){
            
        if (empty(Input::get('username')) || empty(Input::get('password'))){
            $message = "Username and Password is required.";
        }    
        else{

            $validation = $validator->validate($_POST, array(
                'username'  => 'required|min:3',
                'password'  => 'required|min:6'
            ));

            if ($validation->fails()) {
                // handling errors
                $errors = $validation->errors();
                $message = get_form_errors($errors->firstOfAll());
            } 
            else {
                // validation passes    
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
            }
        }
    }
}

?>

<?php include_layout_template('header.php'); ?>

<?php if(Input::get('redirect') == "listproperty"){?>
    <h2 class="text-center mb-4 font-weight-bold" style="text-align: center">First thing first. Join Nyumba yanga and list your property</h2>
    <p style="text-align: center;">-----&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Already have an account?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-----</p>
    <h2 class="text-center mb-4 font-weight-bold" style="text-align: center">Log in to Nyumba yanga</h2>
<?php } 
elseif(Input::get('redirect') == "savedproperty" || Input::get('redirect') == "saved"){?>
    <h2 class="text-center mb-4 font-weight-bold" style="text-align: center">Log in or Sign up to save a listing</h2>  
<?php }
else {?>
    <h2 class="text-center mb-4 font-weight-bold" style="text-align: center">Log in to Nyumba yanga</h2>
<?php } ?>

<?php echo output_message($message, "danger"); ?>

<form action="login.php" method="post" autocomplete="off" accept-charset="utf-8">    
    <div class="form-group mb-3">
        <!-- <label for="username" class="d-none">Username</label> -->
        <input type="text" name="username" class="form-control" placeholder="Email, Phone or username" value="<?php echo escape(Input::get('username'))?>" />        
    </div>
    <div class="form-group">
        <!-- <label for="password" class="d-none">Password</label> -->
        <input type="password" name="password" class="form-control" placeholder="Password"/> 
    </div>
   
    <div class="form-group text-center mb-4" style="text-align: center;margin-bottom: .5rem">    
        <input type="hidden" name="token" value="<?php echo Session::generateToken(); ?>"/>
        <button type="submit" class="btn btn-primary btn-block font-weight-bold" style="width: 100%;">LOG IN</button>
    </div>  
    <ul class="menu" style="text-align: center;">
        <li><a href="#" class="small text-muted">Forgot password?</a></li>
        <li><strong>·</strong></li>
        <li><a href="signup.php" class="small text-muted">Join NyumbaYanga?</a></li>
    </ul>    
                 
</form>

<?php //include_layout_template('footer.php'); ?>
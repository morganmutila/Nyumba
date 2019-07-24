<?php 
include '../private/init.php';
include LIB_PATH.DS.'formr'.DS.'class.formr.php';
include PACKAGE_PATH;

if($session->isLoggedIn()) Redirect::to("index.php");

use Rakit\Validation\Validator;
$validator = new Validator;

if(Input::exists()){
    if(Session::checkToken(Input::get('token'))){

        $validation = $validator->validate($_POST, array(
            'username'  => 'required|min:3',
            'password'  => 'required|min:6'
        ));

        if ($validation->fails()) {
            // handling errors
            $errors = $validation->errors();
            $message = implode(", ", $errors->firstOfAll());
        } 
        else {
            // Validation passes    
            // Check the database to see if username / password exists
            $username    = $email = $phone = Input::get('username');
            $password    = Input::get('password');
            $remember_me = Input::get('rememberme') === 'on' ? true : false;

            $found_user = User::authenticate($username, $password, $email, $phone);

            if($found_user){
                $session->login($found_user, $remember_me);
                Redirect::prevPage();
            } else {
                $message = "Log in failed, account does not exist. Please check your password or sign up for a new account</a></strong>";
            }
        }
    }
}
$page_title = "Login - Nyumba Yanga";
?>

<?php layout_template('header.php'); ?>

<?php if(Input::get('redirect') == "addproperty"){?>
    <h2 class="text-center mb-4 font-weight-bold" style="text-align: center"><!-- First thing first. Join Nyumba yanga and list your property --> Start by Joining NyumbaYanga</h2>
    <p style="text-align: center;">-----&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Already have an account?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-----</p>
    <h2 class="text-center mb-4 font-weight-bold" style="text-align: center">Log in to NyumbaYanga</h2>
<?php } 
elseif(Input::get('redirect') == "savedproperty" || Input::get('redirect') == "saved"){?>
    <h2 class="text-center mb-4 font-weight-bold" style="text-align: center">Log in or Sign up to save a listing</h2>  
<?php }
else {?>
    <h2 class="text-center mb-4 font-weight-bold" style="text-align: center">Log in to NyumbaYanga</h2>
<?php } ?>


<?php
    $form = new Formr('bootstrap');

    $form->html5 = true; 
    $form->method = 'POST';

    $html_form  = output_message($message, "text-danger");
    $html_form .= $form->form_open();
    $html_form .= $form->input_text('username',  '', escape(Input::get('username')),'username', 'placeholder="Email, Phone or username" style="background-color:#F8F8F8";');
    $html_form .= $form->input_password('password',  '', escape(Input::get('password')),'password', 'placeholder="Password" style="background-color:#F8F8F8";');
    $html_form .= $form->input_checkbox('rememberme',  'Remember me', 'on','rememberme');
    $html_form .= $form->input_hidden('token', Session::generateToken());
    $html_form .= $form->input_submit('submit', '', 'LOG IN', 'sign_up', 'class="btn-success btn-block font-weight-bold"');
    $html_form .= '<p class="my-3 text-center" style="text-align: center;">By logging in you agree to our<br>Terms and Privacy Policy</p>'; 
    $html_form .= ' <ul class="list-inline" style="text-align: center;">
                        <li class="list-inline-item"><a href="#" class="small text-muted">Forgot password?</a></li>
                        <li class="list-inline-item"><strong>·</strong></li>
                        <li class="list-inline-item"><a href="signup.php" class="small text-muted">Join NyumbaYanga?</a></li>
                     </ul>';  
    $html_form .= $form->form_close();

    // Display the generated Form
    echo $html_form;
?>
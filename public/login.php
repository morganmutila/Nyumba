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

<section class="px-4 mb-5">

    <?php if(Input::get('redirect') == "addproperty"){?>
        <h5 class="text-center my-5 font-weight-bold">Start by Joining NyumbaYanga.</h5>
        <p class="text-center">-----&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Already have an account?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-----</p>
        <h5 class="text-center my-5 font-weight-bold">Log in to NyumbaYanga.</h5>
    <?php } 
    elseif(Input::get('redirect') == "savedproperty" || Input::get('redirect') == "saved"){?>
        <h5 class="text-center my-5 font-weight-bold">Log in or Sign up to save a listing.</h5>  
    <?php }
    else {?>
        <h5 class="text-center my-5 font-weight-bold">To continue, log in to NyumbaYanga.</h5>
    <?php } ?>


    <?php
        $form = new Formr('bootstrap');

        $form->html5 = true; 
        $form->method = 'POST';


        //Display form messages
        $html_form = output_message($message, "text-danger");

        
        $html_form .= $form->form_open();
        $html_form .= $form->input_text('username',  '', escape(Input::get('username')),'username', 'placeholder="Email, Phone or username" style="background-color:#F8F8F8";');
        $html_form .= $form->input_password('password',  '', escape(Input::get('password')),'password', 'placeholder="Password" style="background-color:#F8F8F8";');
        $html_form .= $form->input_checkbox('rememberme',  'Remember me', 'on','rememberme');
        $html_form .= $form->input_hidden('token', Session::generateToken());
        $html_form .= $form->input_submit('submit', '', 'LOG IN', 'sign_up', 'class="btn-success rounded-lg btn-block"');
     
        $html_form .= $form->form_close();

        // Display the generated Form
        echo $html_form;
    ?>
        <p class="my-3 text-center">By logging in you agree to our<br>Terms and Privacy Policy</p>
 
</section>  

<footer class="bg-light py-3 mx-n4">
    <ul class="nav justify-content-center">
        <li class="nav-item"><a href="#" class="nav-link text-success">Forgot password?</a></li>
        <li class="nav-item nav-link px-0 font-weight-bold">·</li>
        <li class="nav-item"><a href="signup.php" class="nav-link text-success">Join NyumbaYanga?</a></li>
    </ul>
</footer>         
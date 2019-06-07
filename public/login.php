<?php 
require '../init.php';
require LIB_PATH.DS.'formr'.DS.'class.formr.php';
require PACKAGE_PATH;

if($session->isLoggedIn()){ Redirect::to("index.php");}


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
$page_title = "Login - Nyumba Yanga";
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


<?php
    $form = new Formr('bootstrap');

    $html_form  = output_message($message, "danger");
    $form->html5 = true; 
    $form->method = 'POST';
    $html_form .= $form->form_open("sign_in");
    $html_form .= $form->input_text('username',  'Username', escape(Input::get('username')),'username', 'placeholder="Email, Phone or username"');
    $html_form .= $form->input_password('password',  'Password', escape(Input::get('password')),'password', 'placeholder="Password"');
    $html_form .= $form->input_hidden('token', Session::generateToken());
    $html_form .= $form->input_submit('submit', '', 'LOG IN', 'sign_up', 'class="btn-success btn-block font-weight-bold"');
    $html_form .= ' <ul class="menu" style="text-align: center;">
                        <li><a href="#" class="small text-muted">Forgot password?</a></li>
                        <li><strong>·</strong></li>
                        <li><a href="signup.php" class="small text-muted">Join NyumbaYanga?</a></li>
                     </ul>';  
    $html_form .= $form->form_close();

    // Display the generated Form
    echo $html_form;
?>
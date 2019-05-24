<?php
require '../init.php';
if($session->isLoggedIn()){ Redirect::to("index.php");}

$page_title = "Sign up - Nyumba Yanga";

if(Input::exists()){
    if(Session::checkToken(Input::get('token'))) {
        $validate = new Validation();
        $validation = $validate->check($_POST, array(
            'first_name' => array(
                'required' => true,
                'text_only'=> true,
                'min' => 3,
                'max' => 45
            ),
            'last_name' => array(
                'required' => true,
                'text_only'=> true,
                'min' => 3,
                'max' => 45
            ),
            'phone' => array(
                'required' => true,
                'min' => 10,
                'max' => 15,
                'number_only' => true,
                'unique' => 'users'
            ),
            'email' => array(
                'required' => true,
                'max' => 50,
                'valid_email' => true,
                'unique' => 'users'
            ),
            'password' => array(
                'required' => true,
                'min' => 6
            )
        ));

        if ($validation->passed()) {
            // Add the user to the database
            $user = new User();
            $user->group_id     = (int)    1;            
            $user->location_id  = (string) 1;
            $user->username     = (string) strtolower(Input::get('first_name') . Input::get('last_name'));
            $user->first_name   = (string) ucfirst(Input::get('first_name'));
            $user->last_name    = (string) ucfirst(Input::get('last_name'));
            $user->phone        = (string) Input::get('phone');
            $user->status       = (string) "active";
            $user->last_login   = (string) "0000-00-00 00:00:00";
            $user->ip           = (string) $_SERVER['REMOTE_ADDR'];
            $user->email        = (string) Input::get('email');
            $user->password     = (string) password_hash(Input::get('password'), PASSWORD_DEFAULT);

            if($user->create()){
                // Log the newly created user
                if($user){
                    $session->login($user);
                    //See if there is a return URL
                    if(Input::get('returnurl')){
                        //Then redirect to the return url
                        Redirect::to("location.php?returnurl={Input::get('returnurl')}");
                    }
                    Session::flash("joined", "Welcome <strong>". $user->fullname() ."</strong>, Thank you for joining Nyumba yanga");
                    Redirect::to('location.php');
                }
            } else{
                $message = "Oops! Something went wrong, please try again";
            }

        } else {
            $message = join(", ", $validation->errors());
        }
    }
}

?>
<?php include_layout_template('header.php'); ?>

<h2 class="text-center mb-4 font-weight-bold" style="text-align: center;margin-bottom: 0;">Join Nyumba Yanga</h2>
<p style="text-align: center;">Nyumba yanga is home to over&nbsp;<?php echo Property::total();?>,000 Houses, Apartments, Flats and Town House's. We are working together to host property listings with owners, renters and buyers.</p>
<form action="signup.php" method="post" autocomplete="off" accept-charset="utf-8">

    <!-- <p style="text-align: center;"><button type="button">Sign up with Facebook</button></p>
    <p style="text-align: center;">--------------- OR --------------</p> -->
    <?php echo output_message($message, "danger"); ?>
    <div class="form-group col-6">    
        <label for="first_name" class="sr-only">First Name</label>
        <input type="text" name="first_name" class="form-control" value="<?php echo escape(Input::get('first_name')); ?>" placeholder="First name"/>
    </div>

    <div class="form-group col-6">    
        <label for="last_name" class="sr-only">Last Name</label>
        <input type="text" name="last_name" class="form-control" value="<?php echo escape(Input::get('last_name')); ?>" placeholder="Last name"/>
    </div>

    <div class="form-group">
        <label for="phone" class="sr-only">Phone Number</label>                
        <input type="text" name="phone" class="form-control" value="<?php echo escape(Input::get("phone")); ?>" placeholder="Phone Number"/>
    </div>

    <div class="form-group">
        <label for="email" class="sr-only">Email Address</label>                
        <input type="text" name="email" class="form-control" value="<?php echo escape(Input::get("email")); ?>" placeholder="Email"/>
    </div>

    <div class="form-group">    
        <label for="password" class="sr-only">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Create password"/>
    </div>
<p class="text-center text-muted py-2 px-4 small">By clicking Sign up, you agree to Nyumba Yanga terms of service and privacy policy</p>
    <div class="form-group mb-2"> 
        <input type="hidden" name="token" value="<?php echo Session::generateToken(); ?>">
        <button type="submit" class="btn btn-primary btn-block font-weight-bold">Sign up</button>
    </div>  
    <p class="my-3 text-center">Already have an account?<a href="login.php" class="small text-muted">&nbsp;Log in</a></p>  
</form>

<?php include_layout_template('footer.php'); ?>
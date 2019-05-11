<?php
require_once '../../init.php';

if($session->isLoggedIn()) {
    Redirect::to('index.php');
}

$login_status_msg = "";

if(Input::exists()) {
    if (Input::get("token")) {
        $validate = new Validation();
        $validation = $validate->check($_POST, array(
            'username' => array('required' => true),
            'password' => array('required' => true)
        ));

        if ($validation->passed()) {
             //log user in
            $user = new User();
            $username =  Input::get('username');
            $password =  Input::get('password');
            $remember = (Input::get('remember') === 'on') ? true : false;

            $found_user = User::authenticate($username, $password);

            if($found_user){
                 Redirect::to("index.php");
            } else {
                $login_status_msg .= "<div class=\"pb-3 text-danger\">The user doesn't exist, please check your username or password</div>";
            }

        } else {
            $login_status_msg .= display_form_errors($validation->errors());
        }
        
    }
}    
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?php echo isset($page_title) ? $page_title : "Admin - Nyumba Yanga"; ?></title>  
  <link rel="shortcut icon" href="assets/images/favicon.png" />
  <link rel="stylesheet" href="assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.addons.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper auth-page">
      <div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one">
        <div class="row w-100">
          <div class="col-lg-4 mx-auto">
            <div class="auto-form-wrapper pb-4">
              <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
                <?php if(!empty($login_status_msg)){echo $login_status_msg;} ?>
                <div class="form-group">
                  <label class="label">Username</label>
                  <div class="input-group">
                    <input type="text" name="username" class="form-control" placeholder="Username">
                    <div class="input-group-append">
                      <span class="input-group-text">
                        <i class="mdi mdi-check-circle-outline"></i>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="label">Password</label>
                  <div class="input-group">
                    <input type="password" name="password" class="form-control" placeholder="Password">
                    <div class="input-group-append">
                      <span class="input-group-text">
                        <i class="mdi mdi-check-circle-outline"></i>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <input type="hidden" name="token" value="<?php echo Token::generate();?>"/>
                    <button class="btn btn-success submit-btn btn-block" type="submit">Login</button>
                </div>
                <div class="form-group d-flex justify-content-between">
                  <div class="form-check form-check-flat mt-0">
                    <label class="form-check-label">
                      <input type="checkbox" class="form-check-input" checked name="remember" id="remember"> Keep me signed in
                    </label>
                  </div>
                  <a href="#" class="text-small forgot-password text-black">Forgot Password</a>
                </div>
              </form>
            </div>
            <p class="footer-text text-center mt-3">Copyright © 2018 Nyumba Yanga. All rights reserved.</p>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->

<?php get_admin_template("jsfiles");?>
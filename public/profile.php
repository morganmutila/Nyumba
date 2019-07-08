<?php
include '../init.php';
$session->comfirm_logged_in("index.php");

$page_title = "Profile";

if(!$username = Input::get('user')){
    Redirect::to('index.php');
}
else{
    $user = new User($username);
    if(!$user->exists()){
        Redirect::to(404);
    }else{
        $data = $user->data();
    }
    ?>
    <h3><?php echo escape($data->username);?></h3>
    <p>First name: <?php echo escape($data->firstname);?></p>
    <p>Last name: <?php echo escape($data->lastname);?></p>
    <?php

}

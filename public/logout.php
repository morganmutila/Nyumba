<?php
include '../init.php';
$session->comfirm_logged_in("index.php");

$session->logout();
Redirect::prevPage();


?>
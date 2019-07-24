<?php
include '../private/init.php';
require_login("index.php");

$session->logout();
Redirect::prevPage();


?>
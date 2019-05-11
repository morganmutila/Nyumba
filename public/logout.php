<?php
require '../init.php';
if(!$session->isLoggedIn()){ Redirect::to("login.php");}

	$session->logout();
	Redirect::to('index.php');
?>
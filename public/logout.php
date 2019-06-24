<?php
require '../init.php';
if(!$session->isLoggedIn()){ Redirect::to("index.php");}
	$session->logout();
	Redirect::to($_SERVER['HTTP_REFERER']);
?>
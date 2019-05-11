<?php
require '../init.php';
if($session->isLoggedIn()){ Redirect::to("index.php");}

$page_title = "Search";
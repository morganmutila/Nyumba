<!DOCTYPE html>
<html lang="en">
<head>  
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title><?php echo page_title($page_title); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.png"/>
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/mdicons.css "rel="stylesheet" type="text/css" />
  </head>
  <body>
    <div id="header">
<<<<<<< HEAD
      <h2>Nyumba Yanga</h2>
        <?php if(!$session->isLoggedIn()): ?>   
            <ul class="menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="list.php">List property</a></li>
                <li><a href="login.php">Log in</a></li>
                <li><a href="signup.php">Sign up</a></li>
=======
      <h3>NyumbaYanga</h3>
        <?php if(!$session->isLoggedIn()): ?>   
            <ul class="menu">
                <li><a href="index.php" style="margin-left: 0">Home</a></li>
                <li><a href="add.php">List Property</a></li>
                <li><a href="login.php">Log In</a></li>
                <li><a href="signup.php">Sign Up</a></li>
>>>>>>> morgan
            </ul>
        <?php else: ?>
            <ul class="menu">
                <li><a href="index.php">Home</a></li>  
<<<<<<< HEAD
                <li><a href="list.php">List property</a></li>
                <?php if($user->propertyCount() >= 1){ ?>          
                    <li><a href="properties.php">My Listings</a></li>
                <?php } ?>
                <li><a href="saved.php">Saved properties</a></li>
                <li><a href="logout.php">Log out&nbsp;(<?php echo $user->initials();?>)</a></li>
=======
                <li><a href="add.php">List Property</a></li>
                <?php if($user->propertyCount() >= 1){ ?>          
                    <li><a href="properties.php">My Listings</a></li>
                <?php } ?>
                <li><a href="saved.php">Saved Properties</a></li>
                <li><a href="logout.php"><?php echo $user->initials();?></a></li>
>>>>>>> morgan
            </ul>
        <?php endif; ?>  
    </div>
    <div id="main">

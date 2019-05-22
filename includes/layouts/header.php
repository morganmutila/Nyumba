<!DOCTYPE html>
<html lang="en">
<head>  
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title><?php echo page_title($page_title); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.png"/>
    <link href="assets/css/style.css" media="all" rel="stylesheet" type="text/css" />
  </head>
  <body>
    <div id="header">
      <h1>Nyumba Yanga</h1>
        <?php if(!$session->isLoggedIn()): ?>   
            <ul class="menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="list.php">List property</a></li>
                <li><a href="saved.php">My Listings</a></li>
                <li><a href="login.php">Log in</a></li>
                <li><a href="signup.php">Sign up</a></li>
            </ul>
        <?php else: ?>
            <ul class="menu">
                <li><a href="index.php">Home</a></li>  
                <?php if($user->propertyCount() > 0){ ?>          
                    <li><a href="properties.php">My Listings</a></li>
                <?php } ?>
                <li><a href="list.php">List property</a></li>
                <li><a href="saved.php">Saved properties</a></li>
                <li><a href="logout.php">Log out&nbsp;(<?php echo $user->initials();?>)</a></li>
            </ul>
        <?php endif; ?>
        <form action="search.php" method="GET" style="width:100%;margin:0;">
            <table style="width: 100%;">
                <tr>
                    <td><input type="text" name="q" value="" placeholder="Search location" style="width: 102%;margin:0;"></td>
                    <td><button type="submit">Search</button></td>
                </tr>
            </table>
        </form>  
    </div>
    <div id="main">

<!DOCTYPE html>
<html lang="en">
<head>  
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title><?php echo page_title($page_title); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.png"/>
    <link href="assets/vendors/mdi/css/materialdesignicons.css "rel="stylesheet" type="text/css" />
    <link href="assets/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/nyumbayanga.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <nav class="navbar navbar-expand-sm navbar-light border-bottom py-3 mb-3">
        <section class="container">
                <a class="navbar-brand mb-1 h3 mr-5 font-weight-bold" href="index.php">NyumbaYanga</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#NY_navbar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="NY_navbar">
                    <form action="search.php" method="GET" class="form-inline">
                        <div class="input-group mr-2">
                            <input type="text" class="form-control-plaintext" readonly value="<?php echo current_user_location();?>" placeholder="<?php echo current_user_location();?>">
                        </div>
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search location" value="<?php echo (Input::get('q')) ? $found_location : ""?>">
                        </div> 
                    </form>           

                    <?php if($session->isLoggedIn()): ?>
                        <div class="navbar-nav ml-auto">
                            <a class="btn btn-outline-success mr-4" href="new.php">Post your Property</a>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"><?php echo $user->fullName();?></a>
                                <div class="dropdown-menu">
                                    <?php if($user->propertyCount() >= 1){ ?> 
                                        <a class="dropdown-item" href="properties.php">My Listings</a>
                                    <?php } ?>  
                                    <a class="dropdown-item" href="saved.php">Saved Properties</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="logout.php">Log out</a>
                                </div>
                            </li>
                        </div>
                    <?php else: ?>  
                        <div class="navbar-nav ml-auto">
                            <a class="btn btn-outline-success mr-4" href="new.php">Post your Property</a>          
                            <a class="btn btn-outline-secondary" href="login.php">Log In</a></li> 
                            <a class="btn btn-outline-secondary ml-3" href="signup.php">Sign Up</a></li>
                        </div>
                    <?php endif; ?>     
                </div>

        </section>
    </nav>

    <section class="container">

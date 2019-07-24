<!DOCTYPE html>
<html lang="en">
<head>  
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title><?php echo page_title($page_title); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.png"/>
    <link href="../assets/vendors/mdi/css/materialdesignicons.css "rel="stylesheet" type="text/css" />
    <link href="../assets/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/nyumbayanga.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <nav class="navbar navbar-expand-sm navbar-light bg-light border-bottom mb-3">
        <a class="navbar-brand mb-1 h3 font-weight-bold" href="index.php">NyumbaYanga</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#NY_navbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="NY_navbar">
            <div class="navbar-nav ml-auto">
                <a class="nav-item nav-link ml-3" href="/listings/index.php">Property listings</a></li>
                <a class="nav-item nav-link ml-3" href="users.php">Users</a></li>
                <a class="nav-item nav-link ml-3" href="logout.php"><?php echo $user->fullName();?></a>
            </div>    
        </div>
    </nav>

    <div class="container-fluid">

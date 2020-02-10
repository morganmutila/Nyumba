<!DOCTYPE html>
<html lang="en">
<?php
    include("head.php");
?>
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

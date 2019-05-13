<html>
  <head>
    <title><?php echo page_title($page_title); ?></title>
    <link href="assets/css/style.css" media="all" rel="stylesheet" type="text/css" />
  </head>
  <body>
    <div id="header">
      <h1>Nyumba Yanga</h1>
        <?php if(!$session->isLoggedIn()): ?>   
            <ul class="menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="list.php">List a property</a></li>
                <li><a href="saved.php">Saved properties</a></li>
                <li><a href="login.php">Log in</a></li>
                <li><a href="signup.php">Sign up</a></li>
            </ul>
        <?php else: ?>
            <ul class="menu">
                <li><a href="index.php">Home</a></li>  
                <?php if($user->propertyCount() >= 1){ ?>          
                    <li><a href="properties.php">My properties</a></li>
                <?php } ?>
                <li><a href="list.php">List a property</a></li>
                <li><a href="saved.php">Saved properties</a></li>
                <li><a href="logout.php">Log out&nbsp;(<?php echo $user->initials();?>)</a></li>
            </ul>
        <?php endif; ?>
        <form action="search.php" method="GET" style="width:100%;padding-left:1.5rem;margin:0;">
            <table style="width: 400px;">
                <tr>
                    <td><input type="text" name="q" value="" placeholder="Search location" style="width: 102%;margin:0;"></td>
                    <td><button type="submit">Search</button></td>
                </tr>
            </table>
        </form>  
    </div>
    <div id="main">

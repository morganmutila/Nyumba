    <noscript>Javascript must be enabled to run this app</noscript>
    <header style="width: 100%; height: 60px;">
        <nav class="navbar-light bg-white fixed-top px-2 d-flex flex-row justify-content-between align-items-center">
            <a href="index.php" class="m-0 font-weight-bold align-top text-success" style="font-size: 1.6rem;height: 60px">
                <span class="p-2 pr-3 d-inline-block">ny</span>
            </a>

            <div id="search-bar" class="flex-grow-1">
                <form action="search.php" method="GET" class="w-100">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control border-0 bg-light rounded-lg" style="height: 36px;" placeholder="Search location" value="<?php echo (Input::get('q')) ? $found_location : ""?>">
                    </div> 
                </form>
            </div>

            <button class="navbar-toggler border-0 ml-3 p-1" type="button" data-toggle="collapse" data-target="#ny_navbar">
                <span class="navbar-toggler-icon" style="width:1.3em;"></span>
            </button>

            <div class="collapse navbar-collapse shadow-lg h-100 bg-white p-3 mb-3 fixed-top" style="left:auto;width: 88%;" id="ny_navbar">
                <button class="navbar-toggler border-0 p-0 mt-n2 float-right" type="button" data-toggle="collapse" data-target="#ny_navbar">
                    <span class="h1 font-weight-light">&times;</span>
                </button>
                <div class="mt-4 px-1 py-3">
                    <?php if($session->isLoggedIn()): ?>
                        <div class="navbar-nav">
                            <span class="font-weight-bold mb-4"><?php echo $user->fullName();?></span>
                            <?php if($user->propertyCount() >= 1){ ?> 
                                <a class="list-item text-reset mb-4" href="properties.php">My Listings</a>
                            <?php } ?>  
                            <a class="list-item text-reset mb-4" href="saved.php">Saved Properties</a>                     
                            <a class="list-item text-reset mb-4" href="new.php">Add your Property</a>
                            <div class="dropdown-divider mx-n4"></div>
                            <a class="list-item text-reset mb-4" href="logout.php">Log out</a>
                        </div>
                    <?php else: ?>  
                        <div class="navbar-nav">
                            <a class="list-item text-reset mb-4" href="new.php">Add your Property</a>                             
                            <a class="list-item text-reset mb-4" href="signup.php">Sign Up</a></li>
                            <a class="list-item text-reset mb-4" href="login.php">Log In</a></li>
                        </div>
                    <?php endif; ?>    
                </div>     
            </div>
        </nav>
    </header>
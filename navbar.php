<?php require_once '_globals.php'; ?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <b>Emery Beans</b>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="/index.php">Home</a>
                </li>

                <?php if($loggedin) { 
                    if($inviteAuthorized) {?>
                <li class="nav-item">
                    <a class="nav-link" href="/upload.php">Upload</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="/invite.php">Invite</a>
                </li>
                <li>
                    <a class="nav-link" href="/admin.php">Admin</a>
                </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link" href="/account.php">Account</a>
                </li>
                <?php } ?>                    
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo ($loggedin == true) ? "/logout.php" : "/login.php" ?>"><?php echo ($loggedin == true) ? "Logout" : "Login" ?> </a>
                </li>
        </ul>
        </div>
    </div>
</nav>
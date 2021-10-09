<?php
require_once '_globals.php';

if($loggedin) {
    header("location: index.php");
    exit;
}

require_once "classes/Database.php";
require_once "classes/User.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$username = $password = '';
$username_err = $password_err = $login_err = '';

if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = strtolower(trim($_POST["username"]));
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        $user->setUserByEmailAddress($username);
        if($user->validatePassword($password) && $user->enabled == true){
            $user->logAccess();

            // Password is correct
            // Store data in session variables
            $_SESSION["loggedin"] = true;
            $_SESSION["inviteAuthorized"] = $user->inviteAuthorized;
            $_SESSION["id"] = $user->id;
            $_SESSION["username"] = $user->emailAddress;                            
            
            // Redirect user to welcome page
            header("location: index.php");
        } else{
            // Password is not valid, display a generic error message
            $login_err = "Invalid username or password.";
        }
    }
}    
// Close connection
$db->close();

?>

<!<!doctype html>
<html lang="en">
    <head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login</title>

        <link href="site.css" rel="stylesheet" type="text/css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anontmous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <style>
            body { font: 14pt sans-serif; }
        </style>
    </head>
    <body>
        <div class="wrapper container">
        <?php include 'navbar.php'; ?>
            <h2>Welcome to Emery Beans!</h2>
            <h2>Login</h2>
            <p>Please fill in your credentials to login.</p>
            
            <?php
            if(!empty($login_err)) {
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }
            ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class=" <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class=" <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
                <p>Forgot your password? <a href="recoverpassword.php">Click here to reset it.</a></p>
            </form>
        </div>
    </body>
</html>

<?php
require_once '_authorized.php';
require_once "classes".DIRECTORY_SEPARATOR."Database.php";
require_once 'classes'.DIRECTORY_SEPARATOR.'Site.php';
require_once "classes".DIRECTORY_SEPARATOR."User.php";
require_once "classes".DIRECTORY_SEPARATOR."Mailer.php";

$database = new Database();
$db = $database->getConnection();
$site = new Site($db);
$user = new User($db);
$mailer = new Mailer($db, $site);

$emailaddress = $emailaddress_err = $password = $displayname = $displayname_err = $notification_success = $notification_failure = '';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["emailaddress"]))) {
        $emailaddress_err = "Please enter an e-Mail address.";
    //} else if (filter_var($_POST["emailaddress"], FILTER_VALIDATE_EMAIL)) {
        //$emailaddress_err = "Please enter a valid e-Mail address.";
    } else {
        $emailaddress = strtolower(trim($_POST["emailaddress"]));
    }
    
    if (empty(trim($_POST["displayname"]))) {
        $displayname_err = "Please enter a name to display.";
    } else {
        $displayname = trim($_POST["displayname"]);
    }

    if(empty($emailaddress_err)) {
        if ($user->setUserByEmailAddress($emailaddress)) {
            $emailaddress_err = "e-Mail address is already registered.";
        }
    }
    
    if (empty($emailaddress_err) && empty($displayname_err)) {
        $password = $user->generatePassword();
        if ($user->createUser($emailaddress, $password, $displayname)) {
            $notification_success = "User created successfully!<br />";
        } else {
            $notification_failure = "Failed to create User.<br />";
        }
    }

    if(empty($emailaddress_err) && empty($displayname_err) && !empty($password) && empty($notification_failure)) {
        if ($mailer->sendInvite($emailaddress, $password)) {
            $notification_success .= "Invite sent successfully!";
        } else {
            $notification_failure .= "Invite failed to send. Mailer Error: {$mailer->errorMessage}";
        }
    }
}
$db->close();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $site->siteName?> - Send Invites</title>

        <link href="site.css" rel="stylesheet" type="text/css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="wrapper container">
            <?php include 'navbar.php'; ?>
            <h2>Welcome to <?php echo $site->siteName?>!</h2>
            <p>Send Invites</p>
            <?php echo empty($emailaddress_err) ? "" : "<div class='alert alert-danger'>" . $emailaddress_err . "</div>";?>
            <?php echo empty($notification_success) ? "" : "<div class='alert alert-success'>" . $notification_success . "</div>";?>
            <?php echo empty($notification_failure) ? "" : "<div class='alert alert-danger'>" . $notification_failure . "</div>";?>
            <div><?php //echo empty($password) ? '' : 'Password: ' . $password;?></div>
            
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label>Enter e-Mail address:</label><br/>
                        <input type="text" name="emailaddress" value="<?php echo empty($emailaddress_err) ? "" : $emailaddress?>" />
                    </div>
                    <div class="form-group">
                        <label>Enter name to display:</label><br/>
                        <input type="text" name="displayname" value="<?php echo empty($displayname_err) ? "" : $displayname?>" />
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Send Invite" />
                    </div>
                </form>
            
        </div>
    </body>
</html>
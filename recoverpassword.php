<?php
require_once '_globals.php';

if($loggedin) {
    header("location: index.php");
    exit;
}

require_once 'classes/Database.php';
require_once 'classes/Site.php';
require_once 'classes/User.php';
require_once 'classes/Mailer.php';

$database = new Database();
$db = $database->getConnection();
$site = new Site($db);
$mailer = new Mailer($db, $site);

$email_err = $password_err = $notification_success = $notification_failure = $recoverHash = $emailAddress = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $recoverHash = getPostOrEmpty("recoverHash");
    if (!empty($recoverHash)) {
        // User submitted the form with new password
        $newPassword = getPostOrEmpty("newPassword");
        $newPasswordVerify = getPostOrEmpty("newPasswordVerify");
        if ($newPassword == $newPasswordVerify && !empty($newPassword)) {
            $user = new User($db);
            if ($user->setUserByRecoveryHash($recoverHash)) {
                $user->recoveryHash = null;
                $user->recoverySendDate = null;
                $user->hashSeed = $user->createHashSeed();
                $user->pwHash = $user->createPasswordHash($user->emailAddress, $newPassword, $user->hashSeed);
                $user->saveUser();
                $notification_success = "Password changed successfully.";
            } else {
                // Recovery Hash is invalid
                $recoverHash = "";
            }
        }
    } else {
        // User submitted the form with email address
        $emailAddress = getPostOrEmpty("emailAddress");
        if (!empty($emailAddress)) {
            $user = new User($db);
            if ($user->setUserByEmailAddress($emailAddress)) {
                $user->recoveryHash = $user->createRecoveryHash();
                $user->recoverySendDate = date("Y-m-d H:i:s");
                $user->saveUser();
                $mailer->sendRecovery($emailAddress, $user->recoveryHash);
                $notification_success = "Please check your email for the recovery link.";
            } else {
                $notification_failure = "Unable to find an account with that email address.";
            }
        } else {
            $email_err = "Please enter an email address.";
        }
    }
} else if($_SERVER["REQUEST_METHOD"] == "GET") {
    $recoverHash = getGetOrEmpty("recoverHash");
    if (!empty($recoverHash)) {
        // User clicked the link in the email
        $user = new User($db);
        if ($user->setUserByRecoveryHash($recoverHash)) {
            $emailAddress = $user->emailAddress;
        } else {
            // Recovery Hash is invalid
            $recoverHash = "";
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
        <title><?php echo $site->siteName?></title>

        <link href="site.css" rel="stylesheet" type="text/css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="wrapper container">
            <?php include 'navbar.php'; ?>
            <h2>Welcome to <?php echo $site->siteName?>!</h2>
            <p>Recover Password</p>

            <?php echo empty($notification_success) ? "" : "<div class='alert alert-success'>" . $notification_success . "</div>";?>
            <?php echo empty($notification_failure) ? "" : "<div class='alert alert-danger'>" . $notification_failure . "</div>";?>

            <?php if (empty($notification_success)) { ?>
            <?php if (!empty($recoverHash) && !empty($emailAddress)) {?>
                <form id="recoverForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="row form-group">
                        <div class="col-md-2">New Password: </div>
                        <div class="col-md-2"><input type="password" id="newPassword" name="newPassword" class="form-control" /></div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2">Re-type password: </div>
                        <div class="col-md-2"><input type="password" id="newPasswordVerify" name="newPasswordVerify" class="form-control" /></div>
                    </div> 
                    <div class="row form-group">
                        <div class="col-md-2"><input type="submit" id="submitForm" value="Submit" class="form-control" /></div>
                    </div>
                    <input type="hidden" id="recoverHash" name="recoverHash" value="<?php echo $recoverHash;?>" />
                </form>
            <?php } else {?>
                <form id="recoverForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="row form-group">
                        <div class="col-md-2">Email Address: </div>
                        <div class="col-md-3"><input type="text" id="emailAddress" name="emailAddress" class="form-control" />
                        <?php if (!empty($email_err)) { 
                            echo "<br /><span class=\"validation-error\">" . $email_err . "</span>";
                        }
                        ?>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2"><input type="submit" id="submitForm" value="Submit" class="form-control" /></div>
                    </div>
                </form>
            <?php } } ?>
        </div>
    </body>
</html>
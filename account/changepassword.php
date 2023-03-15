<?php
require_once '../_authorized.php';

require_once "../classes/Database.php";
require_once '../classes/Site.php';
require_once "../classes/User.php";

$database = new Database();
$db = $database->getConnection();
$site = new Site($db);
$user = new User($db);

$id = $_SESSION["id"];
$user->setUserById($id);

$currpassword = $currpassword_err = "";
$newpassword = $newpassword_err = "";
$confirmpassword = $confirmpassword_err = "";
$notification_error = $notification_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["changepassword"])) {
        if (!empty($_POST["currpassword"])) {
            $currpassword = $_POST["currpassword"];
        } else {
            $currpassword_err = "Please enter your current password.";
        }

        if (!empty($_POST["newpassword"])) {
            $newpassword = $_POST["newpassword"];
        } else {
            $newpassword_err = "Please enter a new password."; 
        }

        if (!empty($_POST["confirmpassword"])) {
            $confirmpassword = $_POST["confirmpassword"];
        } else {
            $confirmpassword_err = "Please confirm your new password.";
        }

        if (empty($currpasscurd_err) && empty($newpassword_err) && empty($confirmpassword_err)) {
            if ($newpassword != $confirmpassword) {
                $notification_error = "Passwords don't match.";
            } else {
                if ($user->validatePassword($currpassword)) {
                    $user->pwHash = $user->createPasswordHash($user->emailAddress, $newpassword, $user->hashSeed);
                    if ($user->saveUser()) {
                        $notification_success = "Password changed successfully.";
                    } else {
                        $notification_error = "Error changing password.";
                    }
                } else {
                    $notification_error = "Current password doesn't match.";
                }
            }
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

        <link href="/site.css" rel="stylesheet" type="text/css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="wrapper container">
            <?php include '../navbar.php'; ?>
            <h2>Welcome to <?php echo $site->siteName?>!</h2>
            <p>Change Password</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <?php echo !empty($notification_error) ? "<div class='alert alert-danger'>" . $notification_error . "</div>" : "";?>
                <?php echo !empty($notification_success) ? "<div class='alert alert-success'>" . $notification_success . "</div>" : "";?>
                <div class="row form-group">
                    <label for="currpassword" class="col-sm-2 align-right">Current Password:</label>
                    <div class="col-sm-2 <?php echo (!empty($currpassword_err)) ? 'is-invalid' : ''; ?>">
                        <input type="password" name="currpassword" value="<?php echo $currpassword;?>" required />
                        <span class="invalid-feedback"><?php echo $currpassword_err; ?></span>
                    </div>
                </div>
                <div class="row form-group">
                    <label for="newpassword" class="col-sm-2 align-right">New Password:</label>
                    <div class="col-sm-2 <?php echo (!empty($newpassword_err)) ? 'is-invalid' : ''; ?>">
                        <input type="password" name="newpassword" value="<?php echo $newpassword;?>" required />
                        <span class="invalid-feedback"><?php echo $newpassword_err; ?></span>
                    </div>
                </div>
                <div class="row form-group">
                    <label for="confirmpassword" class="col-sm-2 pull-right">Confirm Password:</label>
                    <div class="col-sm-2 <?php echo (!empty($confirmpassword_err)) ? 'is-invalid' : ''; ?>">
                        <input type="password" name="confirmpassword" value="<?php echo $confirmpassword;?>" required />
                        <span class="invalid-feedback"><?php echo $confirmpassword_err; ?></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-2"><input type="submit" name="changepassword" value="Change Password" /></div>
                </div>
            </form>
        </div>
    </body>
</html>

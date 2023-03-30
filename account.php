<?php
require_once '_authorized.php';
require_once "classes/Database.php";
require_once 'classes/Site.php';
require_once "classes/User.php";

$database = new Database();
$db = $database->getConnection();
$site = new Site($db);
$user = new User($db);

$emailaddress = $displayname = $update_err = $update_msg = "";
$displayname_err = "";

$id = $_SESSION["id"];
$user->setUserById($id);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $displayname = getPostOrEmpty("displayname");
    if (empty($displayname))
        $displayname_err = "Please enter a value.";

    if (empty($displayname_err)) {
        $user->displayName = $displayname;
        if ($user->saveUser()) {
            $update_msg = "Display name updated successfully.";
        } else {
            $update_err = "Unable to save changes.";
        }
    }
}

$emailaddress = $user->emailAddress;
$displayname = $user->displayName;

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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js" integrity="sha512-RdSPYh1WA6BF0RhpisYJVYkOyTzK4HwofJ3Q7ivt/jkpW6Vc8AurL1R+4AUcvn9IwEKAPm/fk7qFZW3OuiUDeg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </head>
    <body>
        <div class="wrapper container">
            <?php include 'navbar.php'; ?>
            <h2>Welcome to <?php echo $site->siteName?>!</h2>
            <p>Manage Account</p>
 
            <?php
            if(!empty($update_err)) {
                echo '<div class="alert alert-danger">' . $update_err . '</div>';
            }
            if(!empty($update_msg)) {
                echo "<div class=\"alert alert-success\">" . $update_msg . "</div>";
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="row form-group">
                    <label class="col-xs-3 col-sm-3 col-lg-2">e-Mail Address: </label>
                    <div class="col-sm-6"><?php echo $emailaddress;?></div>
                </div>
                <div class="row form-group">
                    <label class="col-xs-3 col-sm-3 col-lg-2">Display Name: </label>
                    <div class="col-sm-3">
                        <input type="text" name="displayname" value="<?php echo $displayname;?>" class="form-control <?php echo (!empty($displayname_err)) ? 'is-invalid' : ''; ?>"/>
                        <span class="invalid-feedback"><?php echo $displayname_err; ?></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-3 col-sm-3 col-lg-2"></div>
                    <div class="col-sm-2">
                        <input class="btn btn-primary" type="submit" id="submitnamechange" value="Update" />
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-2"><a href="account/changepassword.php">Change Password</a></div>
                </div>
                <div class="row">
                    <div class="col-sm-2"><a href="#" id="delete-account">Delete Account</a></div>
                </div>
            </form>
        </div>
        <script type="text/javascript">
            $(function () {
                $("#delete-account").click(function (e) {
                    e.preventDefault();
                    bootbox.confirm("Are you certain you want to delete your account?", function (result) {
                        if (!result) return;

                        document.location = "account/deleteaccount.php?confirm=ok";
                    });
                });
            });
        </script>
    </body>
</html>
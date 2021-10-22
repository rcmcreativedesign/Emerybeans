<?php
require_once '../_authorized.php';
require_once "../classes/Database.php";
require_once "../classes/User.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$user->setUserById($_SESSION["id"]);

$notification = "";
$del_result = $user->deleteUser();

if (!$del_result) {
    $notification = "Unable to delete account";
} else {
    $_SESSION = array();

    session_destroy();
    $loggedin = false;
}

$db->close();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Emery Beans</title>

        <link href="site.css" rel="stylesheet" type="text/css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anontmous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="wrapper container">
            <?php include '../navbar.php'; ?>
            <h2>Welcome to Emery Beans!</h2>
            <?php if (!empty($notification)) {
                echo "<div class=\"alert alert-danger\">{$notification}</div>";
            } else {
            ?>
            <p>Account Deleted!</p>
            <?php } ?>
        </div>
    </body>
</html>

<?php
require_once '_authorized.php';


?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Emery Beans</title>

        <link href="site.css" rel="stylesheet" type="text/css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anontmous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <body>
    <div class="wrapper container">
        <?php include 'navbar.php'; ?>
        <h2>Welcome to Emery Beans!</h2>
        <p>Site Administration</p>
        <div class="row">
            <div class="col-sm-4">
            <ul>
                <li><a href="admin/manageaccounts.php">Manage Accounts</a></li>
                <li><a href="admin/manageinvites.php">Manage Invites</a></li>
                <li><a href="admin/sitesettings.php">Site Settings</a></li>
                <li><a href="admin/statistics.php">Statistics</a></li>
            </ul>
        </div>
    </body>
</html>
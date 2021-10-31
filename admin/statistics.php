<?php
require_once '../_authorized.php';

require_once "../classes/Database.php";
require_once "../classes/User.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$user->setUserById($_SESSION["id"]);

if (!$user->inviteAuthorized) {
    echo "Admin only area";
    exit;
}

/*
SELECT COUNT(id) AS TotalUsers FROM user
SELECT COUNT(id) AS EnabledUsers FROM user WHERE enabled = 1
SELECT COUNT(id) AS ActiveUsers FROM user WHERE lastLoginTimestamp > DATE_SUB(NOW(), INTERVAL 1 MONTH)

*/
$activeTodayUsers = 0;
$activeTodayUsersQuery = "SELECT COUNT(id) AS ActiveTodayUsers FROM user WHERE lastLoginTimestamp > DATE)SUB(NOW(), INTERVAL 1 DAY)";


$newTodayEntry = 0;
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
        <style>
            .entry-image { max-width: 300px; }
        </style>
    </head>
    <body>
        <div class="wrapper container">
            <?php include '../navbar.php'; ?>
            <h2>Welcome to Emery Beans!</h2>
            <p>Site Statistics</p>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                User Statistics
                            </h4>
                        </div>
                        <div class="panel-body">
                            Active Users Today: <?php echo $activeTodayUsers;?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                Entry Statistics
                            </h4>
                        </div>
                        <div class="panel-body">
                            New Entries Today: <?php echo $newTodayEntry;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
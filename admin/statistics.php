<?php
require_once '../_authorized.php';

require_once "../classes/Database.php";
require_once '../classes/Site.php';
require_once "../classes/User.php";
require_once "../classes/Admin.php";

$database = new Database();
$db = $database->getConnection();
$site = new Site($db);
$user = new User($db);
$user->setUserById($_SESSION["id"]);

if (!$user->inviteAuthorized) {
    echo "Admin only area";
    exit;
}

$admin = new Admin($db);

$totalUsers = $admin->getCountOfTotalUsers();
$totalEnabled = $admin->getCountOfEnabledUsers();
$activeTodayUsers = $admin->getCountOfActiveUsersToday();

$newTodayEntry = $admin->getCountOfEntriesCreatedToday();
$totalEntries = $admin->getCountOfTotalEntries();

$mostLikedEntry = $admin->getMostLikedEntry();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $site->siteName?></title>

        <link href="../site.css" rel="stylesheet" type="text/css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="wrapper container">
            <?php include '../navbar.php'; ?>
            <h2>Welcome to <?php echo $site->siteName?>!</h2>
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
                            Active Users Today: <?php echo $activeTodayUsers;?><br/>
                            Total Users/Enabled Users: <?php echo $totalUsers . " / " . $totalEnabled?><br/>
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
                            New Entries Today: <?php echo $newTodayEntry;?><br/>
                            Total Entries: <?php echo $totalEntries;?><br/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="coll-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                Top Statistics
                            </h4>
                        </div>
                        <div class="panel-body">
                            Most Liked Entry: <?php echo $mostLikedEntry;?><br/>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
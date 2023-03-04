<?php
require_once '../_authorized.php';

require_once "../classes/Database.php";
require_once "../classes/User.php";
require_once "../classes/Admin.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$user->setUserById($_SESSION["id"]);

if (!$user->inviteAuthorized) {
    echo "Admin only area";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
}

$admin = new Admin($db);
$userList = $admin->getAllUsers();
$i = 0;
$total = count($userList);
$db->close();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Emery Beans</title>

        <link href="../site.css" rel="stylesheet" type="text/css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anontmous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        
    </head>
    <body>
        <div class="wrapper container">
            <?php include '../navbar.php'; ?>
            <h2>Welcome to Emery Beans!</h2>
            <p>Manage Accounts</p>

            <form id="inviteform" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="row form-group">
                    <div class="col-sm-2">Display Name</div>
                    <div class="col-sm-2">Email Address</div>
                    <div class="col-sm-2">Last Login</div>
                    <div class="col-sm-1">Enabled</div>
                    <div class="col-sm-1">Admin</div>
                    <div class="col-sm-4"></div>
                </div>
                <?php
                if ($total == 0) {
                    echo "<div class=\"row form-group\"><div class=\"col-sm-12\">No records</div></div>";
                }
                while ($i < $total) {
                    $userArray = $userList[$i];
                    echo "<div class=\"row form-group\">\n";
                    echo "  <div class=\"col-sm-2\">" . $userArray[2] . "</div>\n";
                    echo "  <div class=\"col-sm-2\">" . $userArray[1] . "</div>\n";
                    echo "  <div class=\"col-sm-2\">" . $userArray[3] . "</div>\n";
                    echo "  <div class=\"col-sm-1\">" . $userArray[4] . "</div>\n";
                    echo "  <div class=\"col-sm-1\">" . $userArray[5] . "</div>\n";
                    echo "  <div class=\"col-sm-4\"><a class=\"edit-link btn btn-primary\" data-id=\"" . $userArray[0] . "\" href=\"#\">Edit</a>" . 
                         "<a class=\"delete-link btn btn-danger\" data-id=\"" . $userArray[0] . "\" href=\"#\">Delete</a></div>\n";
                    echo "</div>\n";
                    echo "\n";
                    $i++;
                }
                ?>
<!-- 
                <div class="row form-group">
                    <div class="col-sm-2" data-id="" data-type="displayName"></div>
                    <div class="col-sm-2" data-id="" data-type="emailAddress"></div>
                    <div class="col-sm-2" data-id="" data-type="lastLogin"></div>
                    <div class="col-sm-1" data-id="" data-type="enabled"></div>
                    <div class="col-sm-1" data-id="" data-type="admin"></div>
                    <div class="col-sm-4">
                        <a class="edit-link btn btn-primary" href="#">Edit</a>
                        <a class="delete-link btn btn-danger" href="#">Delete</a>
                    </div>
                </div> -->
            </form>
        </div>
        <script type="text/javascript">
            $(function () {
                $(".edit-link").click(function() {
                    var that = this;
                    $(that).text("Save");
                    $(that).parent().find("[data-type='displayName']").empty().append("<input type='text' id='displayName' value='' />");
                });
                
            });
        </script>
    </body>
</html>
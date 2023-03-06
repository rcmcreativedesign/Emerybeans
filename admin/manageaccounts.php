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
                    echo "  <div class=\"col-sm-2\" data-id=\"" . $userArray[0] . "\" data-type=\"displayName\">" . $userArray[2] . "</div>\n";
                    echo "  <div class=\"col-sm-2\" data-id=\"" . $userArray[0] . "\" data-type=\"emailAddress\">" . $userArray[1] . "</div>\n";
                    echo "  <div class=\"col-sm-2\" data-id=\"" . $userArray[0] . "\" data-type=\"lastLogin\">" . $userArray[3] . "</div>\n";
                    echo "  <div class=\"col-sm-1\" data-id=\"" . $userArray[0] . "\" data-type=\"enabled\">" . $userArray[4] . "</div>\n";
                    echo "  <div class=\"col-sm-1\" data-id=\"" . $userArray[0] . "\" data-type=\"admin\">" . $userArray[5] . "</div>\n";
                    echo "  <div class=\"col-sm-4\"><a class=\"edit-link btn btn-primary\" data-id=\"" . $userArray[0] . "\" data-mode=\"edit\" href=\"#\">Edit</a> " . 
                         "<a class=\"cancel-link btn-warning hidden\" data-id=\"" . $userArray[0] . "\" data-mode=\"cancel\" href=\"#\">Cancel</a> " . 
                         "<a class=\"delete-link btn btn-danger\" data-id=\"" . $userArray[0] . "\" href=\"#\">Delete</a></div>\n";
                    echo "</div>\n";
                    echo "\n";
                    $i++;
                }
                ?>
            </form>
        </div>
        <script type="text/javascript">
            $(function () {
                var displayName = [];
                var emailAddress = [];
                // var lastLogin = [];
                var enabled = [];
                var admin = [];

                $(".edit-link").click(function() {
                    var that = this;
                    var id = $(that).data("id");
                    var mode = $(that).data("mode");
                    var parent = $(that).parent().parent();
                    
                    if (mode === "edit") {
                        $(that).text("Save");
                        $(that).data("mode", "save");
                        parent.find(".cancel-link").removeClass("hidden").addClass("btn");
                        
                        displayName[id] = parent.find("[data-type='displayName']").text();
                        parent.find("[data-type='displayName']").empty().append("<input type='text' class='form-control' id='displayName_" + id + "' value='" + displayName[id] +"' />");
                        emailAddress[id] = parent.find("[data-type='emailAddress']").text();
                        parent.find("[data-type='emailAddress']").empty().append("<input type='text' class='form-control' id='emailAddress_" + id + "' value='" + emailAddress[id] + "' />");
                        // lastLogin[id] = parent.find("[data-type='lastLogin']").text();
                        // parent.find("[data-type='lastLogin']").empty().append("<input type='text' class='form-control' id='lastLogin_" + id + "' value='" + lastLogin[id] + "' />");
                        enabled[id] = parent.find("[data-type='enabled']").text();
                        parent.find("[data-type='enabled']").empty().append("<input type='text' class='form-control' id='enabled_" + id + "' value='" + enabled[id] + "' />");
                        admin[id] = parent.find("[data-type='admin']").text();
                        parent.find("[data-type='admin']").empty().append("<input type='text' class='form-control' id='admin_" + id + "' value='" + admin[id] + "' />");
                    } else if (mode === "save") {
                        $(that).text("Edit");
                        $(that).data("mode", "edit");
                        parent.find(".cancel-link").addClass("hidden").removeClass("btn");

                        var displayNameVal = $("#displayName_" + id).val();
                        var emailAddressVal = $("#emailAddress_" + id).val();
                        // var lastLoginVal = $("#lastLogin_" + id).val();
                        var enabledVal = $("#enabled_" + id).val();
                        var adminVal = $("#admin_" + id).val();

                        parent.find("[data-type='displayName']").empty();
                        parent.find("[data-type='displayName']").text(displayNameVal);
                        parent.find("[data-type='emailAddress']").empty();
                        parent.find("[data-type='emailAddress']").text(emailAddressVal);
                        // parent.find("[data-type='lastLogin']").empty();
                        // parent.find("[data-type='lastLogin']").text(lastLoginVal);
                        parent.find("[data-type='enabled']").empty();
                        parent.find("[data-type='enabled']").text(enabledVal);
                        parent.find("[data-type='admin']").empty();
                        parent.find("[data-type='admin']").text(adminVal);
                    }
                });

                $(".cancel-link").click(function() {
                    var that = this;
                    var id = $(that).data("id");
                    var parent = $(that).parent().parent();

                    $(that).addClass("hidden").removeClass("btn");
                    parent.find(".edit-link").text("Edit");
                    parent.find(".edit-link").data("mode", "edit");

                    parent.find("[data-type='displayName']").empty();
                    parent.find("[data-type='displayName']").text(displayName[id]);
                    parent.find("[data-type='emailAddress']").empty();
                    parent.find("[data-type='emailAddress']").text(emailAddress[id]);
                    // parent.find("[data-type='lastLogin']").empty();
                    // parent.find("[data-type='lastLogin']").text(lastLogin[id]);
                    parent.find("[data-type='enabled']").empty();
                    parent.find("[data-type='enabled']").text(enabled[id]);
                    parent.find("[data-type='admin']").empty();
                    parent.find("[data-type='admin']").text(admin[id]);
                });

                $(".delete-link").click(function() {

                });
            });
        </script>
    </body>
</html>
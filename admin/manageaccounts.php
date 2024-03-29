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

function yesNo($value) {
    if ($value == 1)
        return 'Yes';
    return 'No'; 
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
            <p>Manage Accounts</p>

            <div class="alert alert-danger hidden"></div>
            <div class="alert alert-success hidden"></div>

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
                    echo "  <div class=\"col-sm-1\" data-id=\"" . $userArray[0] . "\" data-type=\"enabled\">" . yesNo($userArray[4]) . "</div>\n";
                    echo "  <div class=\"col-sm-1\" data-id=\"" . $userArray[0] . "\" data-type=\"admin\">" . yesNo($userArray[5]) . "</div>\n";
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
            var postUrl = "modifyaccounts.php";
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
                        // value='" + enabled[id] + "'
                        displayName[id] = parent.find("[data-type='displayName']").text();
                        parent.find("[data-type='displayName']").empty().append("<input type='text' class='form-control' id='displayName_" + id + "' value='" + displayName[id] +"' />");
                        emailAddress[id] = parent.find("[data-type='emailAddress']").text();
                        parent.find("[data-type='emailAddress']").empty().append("<input type='text' class='form-control' id='emailAddress_" + id + "' value='" + emailAddress[id] + "' />");
                        // lastLogin[id] = parent.find("[data-type='lastLogin']").text();
                        // parent.find("[data-type='lastLogin']").empty().append("<input type='text' class='form-control' id='lastLogin_" + id + "' value='" + lastLogin[id] + "' />");
                        enabled[id] = intFromYesNo(parent.find("[data-type='enabled']").text());
                        parent.find("[data-type='enabled']").empty().append("<select class='form-control' id='enabled_" + id + "'><option value='1'" + (enabled[id] == 1 ? " selected" : "") + ">Yes</option><option value='0'" + (enabled[id] == 0 ? " selected" : "") + ">No</option></select>");
                        admin[id] = intFromYesNo(parent.find("[data-type='admin']").text());
                        parent.find("[data-type='admin']").empty().append("<select class='form-control' id='admin_" + id + "'><option value='1'" + (admin[id] == 1 ? " selected" : "") + ">Yes</option><option value='0'" + (admin[id] == 0 ? " selected" : "") + ">No</option></select>");
                    } else if (mode === "save") {
                        var displayNameVal = $("#displayName_" + id).val();
                        var emailAddressVal = $("#emailAddress_" + id).val();
                        // var lastLoginVal = $("#lastLogin_" + id).val();
                        var enabledVal = $("#enabled_" + id).val();
                        var adminVal = $("#admin_" + id).val();
                        $.post(postUrl, { id: id, displayName: displayNameVal, emailAddress: emailAddressVal, enabled: enabledVal, admin: adminVal }, function (data) {
                            if (data) {
                                var response = JSON.parse(data);
                                if (response.success) {
                                    $(that).text("Edit");
                                    $(that).data("mode", "edit");
                                    parent.find(".cancel-link").addClass("hidden").removeClass("btn");

                                    parent.find("[data-type='displayName']").empty();
                                    parent.find("[data-type='displayName']").text(displayNameVal);
                                    parent.find("[data-type='emailAddress']").empty();
                                    parent.find("[data-type='emailAddress']").text(emailAddressVal);
                                    // parent.find("[data-type='lastLogin']").empty();
                                    // parent.find("[data-type='lastLogin']").text(lastLoginVal);
                                    parent.find("[data-type='enabled']").empty();
                                    parent.find("[data-type='enabled']").text(intToYesNo(enabledVal));
                                    parent.find("[data-type='admin']").empty();
                                    parent.find("[data-type='admin']").text(intToYesNo(adminVal));

                                } 
                                notify(response.message, response.success);
                            } else {
                                notify("Failed to get a response from the server.", false);
                            }
                        });
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
                    parent.find("[data-type='enabled']").text(intToYesNo(enabled[id]));
                    parent.find("[data-type='admin']").empty();
                    parent.find("[data-type='admin']").text(intToYesNo(admin[id]));
                });

                $(".delete-link").click(function() {
                    var that = this;
                    var id = $(that).data("id");
                    
                    $.post(postUrl, { id: id }, function (data) {
                        var response = JSON.parse(data);
                        if (response.success) {
                            var parent = $(that).parent().parent();
                            parent.remove();
                        }
                        notify(response.message, response.success);
                    });
                });
            });

            function notify(message, success) {
                if (success) {
                    $(".alert-danger").addClass("hidden").text();
                    $(".alert-success").removeClass("hidden").text(message);
                } else {
                    $(".alert-danger").removeClass("hidden").text(message);
                    $(".alert-success").addClass("hidden").text();
                }
            }

            function intFromYesNo(value) {
                if (value == "Yes")
                    return 1;
                return 0;
            }

            function intToYesNo(value) {
                if (value == 1)
                    return "Yes";
                return "No";
            }
        </script>
    </body>
</html>
<?php
require_once '../_authorized.php';

require_once "../classes/Database.php";
require_once "../classes/User.php";
require_once "../classes/Admin.php";
require_once "../classes/Mailer.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$user->setUserById($_SESSION["id"]);

if (!$user->inviteAuthorized) {
    echo "Admin only area";
    exit;
}

$admin = new Admin($db);
$update_err = $update_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty(trim($_POST["inviteId"])) && !empty(trim($_POST["linkType"]))) {
        $inviteId = trim($_POST["inviteId"]);
        $linkType = trim($_POST["linkType"]);
        $inviteUser = new User($db);
        $inviteUser->setUserById($inviteId);
        if ($linkType == "delete") {
            if ($inviteUser->deleteUser()) {
                $update_msg = "Invite deleted successfully";
            } else {
                $update_err = "Failed to delete invite";
            }
        } else if ($linkType == "resend") {
            $password = $inviteUser->generatePassword();
            $inviteUser->pwHash = $inviteUser->createPasswordHash($inviteUser->emailAddress, $password, $inviteUser->hashSeed);
            $inviteUser->saveUser();

            $mailer = new Mailer($db);
            if ($mailer->sendInvite($inviteUser->emailAddress, $password)) {
                $update_msg = "Invite resent successfully!";
            } else {
                $update_err = "Invite failed to send. Mailer Error: {$mailer->errorMessage}";
            }
        }
    }
}

// Get all open invites
$invites = $admin->getOpenInvites();
$i = 0;
$total = count($invites);
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
            <p>Manage Invites</p>

            <?php
            if(!empty($update_err)) {
                echo '<div class="alert alert-danger">' . $update_err . '</div>';
            }
            if(!empty($update_msg)) {
                echo "<div class=\"alert alert-success\">" . $update_msg . "</div>";
            }
            ?>

            <form id="inviteform" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="row form-group">
                    <div class="col-sm-2">Display Name</div>
                    <div class="col-sm-2">Email Address</div>
                    <div class="col-sm-2">Invite Timestamp</div>
                    <div class="col-sm-4"></div>
                </div>
                <?php
                if ($total == 0) {
                    echo "<div class=\"row form-group\"><div class=\"col-sm-10\">No records</div></div>";
                }
                while ($i < $total) {
                    $invitearray = $invites[$i];
                    echo "<div class=\"row form-group\">\n";
                    echo "  <div class=\"col-sm-2\">" . $invitearray[2] . "</div>\n";
                    echo "  <div class=\"col-sm-2\">" . $invitearray[1] . "</div>\n";
                    echo "  <div class=\"col-sm-2\">" . $invitearray[3] . "</div>\n";
                    echo "  <div class=\"col-sm-4\"><a class=\"resend-link btn btn-primary\" data-id=\"" . $invitearray[0] . "\" href=\"#\">Resend</a>" . 
                         "<a class=\"delete-link btn btn-danger\" data-id=\"" . $invitearray[0] . "\" href=\"#\">Delete</a></div>\n";
                    echo "</div>\n";
                    echo "\n";
                    $i++;
                }
                ?>
                <input type="hidden" name="inviteId" id="inviteId" />
                <input type="hidden" name="linkType" id="linkType" />
            </form>
        </div>
        <script type="text/javascript">
            $(function () {
                $(".delete-link").click(function (e) {
                    e.preventDefault();
                    var id = $(e.currentTarget).data("id");
                    $("#inviteId").val(id);
                    $("#linkType").val("delete");
                    $("#inviteform").submit();
                });
                $(".resend-link").click(function (e) {
                    e.preventDefault();
                    var id = $(e.currentTarget).data("id");
                    $("#inviteId").val(id);
                    $("#linkType").val("resend");
                    $("#inviteform").submit();
                })
            });
        </script>
    </body>
</html>
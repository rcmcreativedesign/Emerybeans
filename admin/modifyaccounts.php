<?php
require_once '../_authorized.php';

require_once "../classes/Database.php";
require_once "../classes/User.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$user->setUserById($_SESSION["id"]);

if (!$user->inviteAuthorized) {
    $response = array("success" => false);
    echo json_encode($response);
    $db->close();
    exit;
}

$notification_failure = $notification_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idVal = getPostOrEmpty("id");
    $displayNameVal = getPostOrEmpty("displayName");
    $emailAddressVal = strtolower(getPostOrEmpty("emailAddress"));
    $enabledVal = getPostOrEmpty("enabled");
    $adminVal = getPostOrEmpty("admin");

    if (empty($idVal)) {
        $notification_failure = "Missing Id";
    } else {
        $updateUser = new User($db);
        if ($updateUser->setUserById($idVal)) {
            if (empty($emailAddressVal)) {
                // Delete
                if ($updateUser->deleteUser()) {
                    $notification_success = "User deleted successfully";
                } else {
                   $notification_failure = "Failed to delete user";
                }
            } else {
                // Update
                $updateUser->displayName = $displayNameVal;
                $updateUser->emailAddress = $emailAddressVal;
                $updateUser->enabled = $enabledVal;
                $updateUser->inviteAuthorized = $adminVal;
                if ($updateUser->saveUser()) {
                    $notification_success = "User updated successfully";
                } else {
                    $notification_failure = "Unable to update user";
                }
            }
        } else {
            $notification_failure = "Failed to load user";
        }
    }
}

if (!empty($notification_failure)) {
    $response = array("success" => false, "message" => $notification_failure);
    echo json_encode($response);
} else if (!empty($notification_success)) {
    $response = array("success" => true, "message" => $notification_success);
    echo json_encode($response);
}
$db->close();

function getPostOrEmpty($postVal) {
    if (isset($_POST[$postVal])) {
        if (empty(trim($_POST[$postVal]))) {
            return "";
        } else {
            return trim($_POST[$postVal]);
        }
    } else {
        return "";
    }
}
?>
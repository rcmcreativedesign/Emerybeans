<?php
require_once '_authorizedpartial.php';
require_once "classes/Database.php";
require_once "classes/User.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$link = $db;

$entryId = $_GET['id'];
$image = '';
$query = 'SELECT imageBinary, type FROM entry WHERE id = ?';

if ($stmt = mysqli_prepare($link, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $param_id);

    $param_id = $entryId;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $image, $imagetype);
            mysqli_stmt_fetch($stmt);
        }
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($link);
header('content-type:' . $imagetype);
echo $image;
?>
<?php
require_once '_authorizedpartial.php';
require_once "classes/Database.php";
require_once "classes/Entry.php";

$database = new Database();
$db = $database->getConnection();
$entry = new Entry($db);

$entryId = $_GET['id'];
$entry->setEntryById($entryId);

$image = $entry->getImage();

$db->close();

header('content-type:' . $entry->type);
echo $image;
?>
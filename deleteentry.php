<?php
require_once '_authorized.php';
require_once "classes/Database.php";
require_once "classes/Entry.php";

$database = new Database();
$db = $database->getConnection();
$entry = new Entry($db);

$entryId = $_POST["id"];
$entry->setEntryById($entryId);
$response = array("success" => $entry->deleteEntry());
echo json_encode($response);
$db->close();
?>
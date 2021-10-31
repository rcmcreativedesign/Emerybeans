<?php
require_once "_authorized.php";
require_once "classes/Database.php";
require_once "classes/Entry.php";

$database = new Database();
$db = $database->getConnection();
$entry = new Entry($db);

$entryId = $_POST["id"];
$caption = $_POST["caption"];

$entry->setEntryById($entryId);
$entry->comments = $caption;
$entry->saveEmtry();
$db->close();
?>
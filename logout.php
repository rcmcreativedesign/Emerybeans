<?php
require_once '_authorized.php';

$_SESSION = array();

session_destroy();

header("location: index.php");
exit;
?>
<?php
require_once '_globals.php';

if(!$loggedin) {
    header("location: index.php");
    exit;
}
?>

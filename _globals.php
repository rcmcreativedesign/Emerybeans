<?php
$loggedin = false;
$inviteAuthorized = false;


session_start();
if (isset($_SESSION["loggedin"])) {
    $loggedin = $_SESSION["loggedin"];
}

if (isset($_SESSION["inviteAuthorized"])) {
    $inviteAuthorized = $_SESSION["inviteAuthorized"];
}


function getEntryCount($link) {
    $result_count = 0;
    $entry_query = "SELECT COUNT(id) FROM entry";
    if ($result = $link->query($entry_query)) {
        $result_count = $result->fetch_row()[0];
    }
    return $result_count;
}

?>

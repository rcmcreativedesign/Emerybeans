<?php
    require_once '_authorized.php';
    require_once "classes/Database.php";
    require_once "classes/Entry.php";
    
    $database = new Database();
    $db = $database->getConnection();
    $entry = new Entry($db);
    
    $entryId = $_POST["id"];
    $userId = $_SESSION["id"];

    $success = false;
    $likedList = "";

    $entry->setEntryById($entryId);

    if ($entry->hasLiked($userId)) {
        $result_array = array("success"=>true, "likedList"=>implode("<br/>", $entry->getLikedList()), "responseMsg"=>"Already liked");
        echo json_encode($result_array);
        $db->close();
        exit;
    } else {
        if ($entry->setEntryLiked($userId)) {
            $result_array = array("success"=>true, "likedList"=>implode("<br/>", $entry->getLikedList()), "responseMsg"=>"");
            echo json_encode($result_array);
            $db->close();
            exit;
        } else {
            $result_array = array("success"=>false, "likedList"=>implode("<br/>", $entry->getLikedList()), "responseMsg"=>"");
            echo json_encode($result_array);
            $db->close();
            exit;
        }
    }
?>
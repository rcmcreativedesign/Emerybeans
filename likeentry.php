<?php
    require_once '_authorized.php';
    require_once "classes/Database.php";
    require_once "classes/User.php";
    
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    
    $link = $db;
    
    $entryId = $_POST["id"];
    $userId = $_SESSION["id"];

    $success = false;
    $likedList = "";


    $stmt = $link->prepare("SELECT COUNT(timestamp) AS checkCount FROM entrylike WHERE entryId = ? AND userId = ?");
    $stmt->bind_param('ii', $entryId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc();

    if($count["checkCount"] > 0) {
        $stmt->close();
        $likedList = getLikedList($link, $entryId);
        $link->close();
        //var_dump($likedList);
        $result_array = array("success"=>true, "likedList"=>implode("<br/>", $likedList), "responseMsg"=>"Already liked");
        echo json_encode($result_array);
        exit;
    } else {
        $stmt = $link->prepare("INSERT INTO entrylike (entryid, userid) VALUES (?, ?)");
        $stmt->bind_param('ii', $entryId, $userId);
        $stmt->execute();
        $likedList = getLikedList($link, $entryId);
        $link->close();
        $result_array = array("success"=>true, "likedList"=>implode("<br/>", $likedList), "responseMsg"=>"");
        echo json_encode($result_array);
        exit;
    }
 ?>
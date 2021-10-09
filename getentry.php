<?php
require_once '_authorizedpartial.php';
require_once "classes/Database.php";
require_once "classes/Entry.php";

$database = new Database();
$db = $database->getConnection();
$entry = new Entry($db);

$link = $db;


$entryId = $_GET["id"];
$userId = $_SESSION["id"];

$canEdit = $inviteAuthorized;
$hasLiked = false;
$hasViewed = false;

$entry->setEntryById($entryId);

$userstats_query = "SELECT CASE WHEN ev.timestamp IS NULL THEN 0 ELSE 1 END AS HasViewed, CASE WHEN el.timestamp IS NULL THEN 0 ELSE 1 END AS HasLiked FROM `entry` AS e LEFT JOIN (SELECT entryid, timestamp FROM entryview WHERE userid = ?) AS ev ON e.id = ev.entryid LEFT JOIN (SELECT entryid, timestamp FROM entrylike WHERE userid = ?) AS el ON e.id = el.entryid WHERE e.id = ?";
if ($stmt = $link->prepare($userstats_query)) {
    $stmt->bind_param("iii", $userId, $userId, $entryId);

    if ($stmt->execute()) {
        if(strlen($stmt->error) != 0) {
            echo $stmt->error;
            exit;
        }
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($hasviewed_val, $hasliked_val);
            $stmt->fetch();

            $hasLiked = $hasliked_val;
            $hasViewed = $hasviewed_val;
        }
    }
    $stmt->close();
}

$likedresults = implode("<br/>", $entry->getLikedList());

// Register view
if (!$hasViewed) {
    $viewquery = "INSERT INTO entryview (entryId, userId) VALUES (?, ?)";
    if ($stmt = $link->prepare($viewquery)) {
        $stmt->bind_param("ii", $entryId, $userId);
        $stmt->execute();
        $stmt->close();
    }
}

$link->close();

$heartClass = $hasLiked ? "bi-heart-fill" : "bi-heart";
?>

<div id="entry<?php echo $entryId?>">
    <div class="row form-group">
        <img id="entryImage<?php echo $entryId?>" class="entry-image" />
    </div>
    <div class="row">
        <div>
            <?php echo !$hasViewed ? "<span style=\"color: red;\">new </span>" : "";?><span class="timestamp"><?php echo date_format($entry->uploadTimestampDisplay(), "l, F j, Y g:i:s a");?></span>
            <a class="heart-icon" data-entryid="<?php echo $entryId?>" href="#" title="Like" data-content="<?php echo $likedresults;?>"><i class="bi <?php echo $heartClass?>" style="color: red;"></i></a>
            <?php echo $canEdit ? "<a class=\"edit-icon\" data-entryid=\"{$entryId}\" href=\"#\" title=\"Edit\"><i class=\"bi bi-pencil-fill\"></i></a>" : ""; ?>
        </div>
    </div>
    <div class="row">
        <span class="caption"><?php echo $entry->comments?></span>
    </div>
</div>

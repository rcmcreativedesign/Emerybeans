<?php
require_once '_authorizedpartial.php';
require_once "classes/Database.php";
require_once "classes/Entry.php";

$database = new Database();
$db = $database->getConnection();
$entry = new Entry($db);

$entryId = $_GET["id"];
$userId = $_SESSION["id"];

$canEdit = $inviteAuthorized;

$entry->setEntryById($entryId);
$hasLiked = $entry->hasLiked($userId);
$hasViewed = $entry->hasViewed($userId);

$likedresults = implode("<br/>", $entry->getLikedList());

// Register view
if (!$hasViewed) {
    $entry->setEntryViewed($userId);
}

$db->close();

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
            <?php echo $canEdit ? "<a class=\"edit-icon\" data-toggle=\"modal\" data-target=\"#edit-popup\" data-entryid=\"{$entryId}\" data-caption=\"{$entry->comments}\" href=\"#\" title=\"Edit\"><i class=\"bi bi-pencil-fill\"></i></a><a class=\"delete-icon\" data-entryid=\"{$entryId}\" href=\"#\" title=\"Delete\"><i class=\"bi bi-trash-fill\"></i></a>" : ""; ?>
        </div>
    </div>
    <div class="row">
        <span class="caption"><?php echo $entry->comments?></span>
    </div>
</div>

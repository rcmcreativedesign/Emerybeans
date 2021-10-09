<?php
require_once '_authorized.php';
require_once "classes/Database.php";
require_once "classes/User.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$link = $db;

$page_size = isset($_GET["pagesize"]) ? $_GET["pagesize"] : 10;
$current_page = isset($_GET["page"]) ? $_GET["page"] : 0;
$offset = ($current_page - 1) * $page_size;
if ($offset < 0)
    $offset = 0;
$entry_count = getEntryCount($link);
//echo "Page Size: {$page_size}, Current Page: {$current_page}, Offset: {$offset}, Entry Count: {$entry_count}";

if ($entry_count >= $offset) {
    $query = "SELECT id FROM entry ORDER BY uploadTimestamp DESC LIMIT ?, ?";
    if ($stmt = $link->prepare($query)) {
        $stmt->bind_param("ii", $offset, $page_size);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $allIds = $result->fetch_all(MYSQLI_NUM);
            $stmt->close();
            $link->close();
            echo json_encode(FixEntries($allIds));
            exit;
        }
        $stmt->close();
    }
}
$link->close();
echo json_encode([]);

function FixEntries($entry_array) {
    $array = array();
    for ($i = 0; $i < count($entry_array); $i++) {
        $array[] = $entry_array[$i][0];
    }
    return $array;
}
?>
<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "libs/jwt/BeforeValidException.php";
include_once "libs/jwt/ExpiredException.php";
include_once "libs/jwt/SignatureInvalidException.php";
include_once "libs/jwt/JWT.php";
use \Firebase\JWT\JWT;

include_once "../classes/Database.php";
include_once "../classes/User.php";
include_once "../classes/Entry.php";
include_once "_jwt.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$entry = new Entry($db);

$data = json_decode(file_get_contents("php://input"));

if (!$data) {
    http_response_code(500);
    echo json_encode(array("message"=>"Malformed request."));
    exit;
}

// Validate input
$jwt = isset($data->jwt) ? $data->jwt : "";
$page = isset($data->Page) ? $data->Page : 1;
$page_size = isset($data->PageSize) ? $data->PageSize : 10;

if (empty($jwt)) {
    $db->close();
    http_response_code(404);
    echo json_encode(array("message" => "Missing JWT token."));
    exit;
}

$offset = ($page - 1) * $page_size;
if ($offset < 0)
    $offset = 0;
$entry_count = getEntryCount($db);

// Process JWT token
try {
    $decoded = JWT::decode($data->jwt, $key, array("HS256"));

    http_response_code(200);
    if ($entry_count >= $offset) {
        $query = "SELECT id FROM entry ORDER BY uploadTimestamp DESC LIMIT ?, ?";
        if ($stmt = $db->prepare($query)) {
            $stmt->bind_param("ii", $offset, $page_size);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $allIds = $result->fetch_all(MYSQLI_NUM);
                $stmt->close();
                $db->close();
                echo json_encode(array("entries" => FixEntries($allIds)));
                exit;
            }
            $stmt->close();
        }
    }
    $db->close();
    echo json_encode(array("entries" => []));
} catch (Exception $e)
{
    http_response_code(401);
    echo json_encode(array("message" => "Invalid JWT token."));
}

function getEntryCount($link) {
    $result_count = 0;
    $entry_query = "SELECT COUNT(id) FROM entry";
    if ($result = $link->query($entry_query)) {
        $result_count = $result->fetch_row()[0];
    }
    return $result_count;
}

function FixEntries($entry_array) {
    $array = array();
    for ($i = 0; $i < count($entry_array); $i++) {
        $array[] = $entry_array[$i][0];
    }
    return $array;
}

?>
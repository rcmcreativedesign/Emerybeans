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
$entryId = isset($data->EntryId) ? $data->EntryId : "";

if (empty($jwt)) {
    $db->close();
    http_response_code(404);
    echo json_encode(array("message" => "Missing JWT token."));
    exit;
}

if (empty($entryId)) {
    $db->close();
    http_response_code(404);
    echo json_encode(array("message" => "Missing EntryId."));
    exit;
}

// Process JWT token
try {
    $decoded = JWT::decode($data->jwt, $key, array("HS256"));
    $user->setUserById($decoded->data->UserId);
    $entry->setEntryById($data->EntryId);

    http_response_code(200);
    echo json_encode(array("id" => $entry->id, "caption" => $entry->comments, "uploadTimestamp" => $entry->uploadTimestampDisplay()));
} catch (Exception $e)
{
    http_response_code(401);
    echo json_encode(array("message" => "Invalid JWT token."));
}
?>
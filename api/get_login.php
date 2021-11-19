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

include_once "_jwt.php";
include_once "../classes/Database.php";
include_once "../classes/User.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if ($data) {
    $user->setUserByEmailAddress($data->email);

    if ($user->id && $user->validatePassword($data->password)) {
        if ($user->enabled == true) {
            $token = array(
                "iat"=>$issued_at,
                "exp"=>$expiration_time,
                "iss"=>$issuer,
                "data"=>array("UserId"=>$user->id)
            );

            http_response_code(200);

            $jwt = JWT::encode($token, $key);
            echo json_encode(array("message"=>"Successful login.", "jwt"=>$jwt));
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Account disabled"));
        }
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Invalid username or password."));
    }
} else {
    http_response_code(500);
    echo json_encode(array("message"=>"Malformed request"));
}
?>
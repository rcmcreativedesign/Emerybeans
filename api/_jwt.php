<?php
// include_once "libs/jwt/BeforeValidException.php";
// include_once "libs/jwt/ExpiredException.php";
// include_once "libs/jwt/SignatureInvalidException.php";
// include_once "libs/jwt/JWT.php";
// use \Firebase\JWT\JWT;
require_once "../classes/Database.php";
require_once '../classes/Site.php';

$database = new Database();
$db = $database->getConnection();
$site = new Site($db);
$db->close();

$key = "Emerybeans12345";
$issued_at = time();
$expiration_time = $issued_at + (60 * 60);
$issuer = $site->siteUrl . "/api";
?>
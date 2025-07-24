<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM dishes ORDER BY category, name";
$stmt = $db->prepare($query);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $dishes = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($dishes, $row);
    }
    
    http_response_code(200);
    echo json_encode($dishes);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No dishes found."));
}
?> 
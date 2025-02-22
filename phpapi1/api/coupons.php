<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requeted-With");

include "../connect.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getCoupons();
        break;
    case 'POST':
        if(isset($_GET['id'])){
            updateCoupons();
        }else{
            createCoupons();
        }
        break;
    case 'PUT':
        deleteCoupons();
        break;
    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

function getCoupons(){
    global $pdo;
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $stmt = $pdo->prepare("select * from Coupons where id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result){
            echo json_encode($result);
        }else {
            echo json_encode(["message" => "Coupons not found"]);
        }
    }else{
        $stmt = $pdo->query("select * from Coupons");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }
}

function createCoupons() {
    global $pdo;

    // Ensure error reporting is enabled for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Decode JSON input from php://input
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if required fields are present
    if (isset($data['code']) && isset($data['discount']) && isset($data['expires_at'])) {
        // Prepare the SQL statement
        $stmt = $pdo->prepare("INSERT INTO coupons (code, discount, expires_at, active) VALUES (:code, :discount, :expires_at, :active)");

        // Execute the statement with values
        if ($stmt->execute([
            ':code' => $data['code'],                 // Use code from JSON input
            ':discount' => $data['discount'],         // Use discount from JSON input
            ':expires_at' => $data['expires_at'],     // Use expires_at from JSON input
            ':active' => 1,                           // Set active to 1 by default
        ])) {
            echo json_encode(['message' => "Coupons created successfully"]);
        } else {
            $errorInfo = $stmt->errorInfo(); // Get error info for debugging
            echo json_encode(['message' => "Unable to create Coupons", 'error' => $errorInfo]);
        }
    } else {
        echo json_encode(['message' => "Required fields are missing"]);
    }
}


function updateCoupons(){
    global $pdo;
    $id = $_GET['id'];
    $data = json_decode(file_get_contents('php://input'), true);

    $stmt = $pdo->prepare("update Coupons set code = :code, discount =:discount where id = :id");
    if($stmt->execute([
        ":code" => $data['code'],
        ":discount" => $data['discount'],
        ":id" => $id
    ])){
        echo json_encode(['message' => "Coupons updated successfully"]);
    }else{
        echo json_encode(['message' => "Unable to update Coupons"]);
    }
}

function deleteCoupons(){
    global $pdo;
    $id = $_GET['id'];

    $stmt = $pdo->prepare("update Coupons set active = 0 where id = :id");
    if($stmt->execute([':id' => $id])){
        echo json_encode(['message' => "Coupons deleted successfully"]);
    } else {
        echo json_encode(['message' => "Unable to delete Coupons"]);
    }
}
?>
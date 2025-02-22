<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requeted-With");

include "../connect.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getOrders();
        break;
    case 'POST':
        if(isset($_GET['id'])){
            // updateOrders();
        }else{
            createOrders();
        }
        break;
    case 'PUT':
        // deleteOrders();
        break;
    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

function getOrders(){
    global $pdo;
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $stmt = $pdo->prepare("select * from Orders where id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result){
            echo json_encode($result);
        }else {
            echo json_encode(["message" => "Orders not found"]);
        }
    }else{
        $stmt = $pdo->query("select * from Orders");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }
}

function createOrders(){
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);

    // Debugging: Check incoming data
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['message' => "Invalid JSON input"]);
        return;
    }

    // Check if 'id' is present
    if (!isset($data['id'])) {
        echo json_encode(['message' => "User ID is required"]);
        return;
    }

    // Check if user exists
    $userId = $data['id'];
    $userCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = :id");
    $userCheckStmt->execute([':id' => $userId]);
    $userExists = $userCheckStmt->fetchColumn();

    if (!$userExists) {
        echo json_encode(['message' => "User ID does not exist"]);
        return;
    }

    // Prepare the insert statement
    $stmt = $pdo->prepare("INSERT INTO Orders (user_id, total_price, status, active) VALUES (:user_id, :total_price, :status, :active)");

    // Execute the statement
    try {
        if($stmt->execute([
            ':user_id' => $userId,
            ':total_price' => $data['total_price'],
            ':status' => $data['status'],
            ':active' => 1
        ])){
            echo json_encode(['message' => "Order created successfully"]);
        } else {
            echo json_encode(['message' => "Unable to create order"]);
        }
    } catch (PDOException $e) {
        echo json_encode(['message' => "Database error: " . $e->getMessage()]);
    }
}




// function updateOrders(){
//     global $pdo;
//     $id = $_GET['id'];
//     $data = json_decode(file_get_contents('php://input'), true);

//     $stmt = $pdo->prepare("update Orders set name = :name where id = :id");
//     if($stmt->execute([
//         ":name" => $data['name'],
//         ":id" => $id
//     ])){
//         echo json_encode(['message' => "Orders updated successfully"]);
//     }else{
//         echo json_encode(['message' => "Unable to update Orders"]);
//     }
// }

// function deleteOrders(){
//     global $pdo;
//     $id = $_GET['id'];

//     $stmt = $pdo->prepare("update Orders set active = 0 where id = :id");
//     if($stmt->execute([':id' => $id])){
//         echo json_encode(['message' => "Orders deleted successfully"]);
//     } else {
//         echo json_encode(['message' => "Unable to delete Orders"]);
//     }
// }
?>
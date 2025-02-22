<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requeted-With");

include "../connect.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getUsers();
        break;
    case 'POST':
        if(isset($_GET['id'])){
            updateUsers();
        }else{
            createUsers();
        }
        break;
    case 'PUT':
        deleteUsers();
        break;
    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

function getUsers(){
    global $pdo;
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $stmt = $pdo->prepare("select * from Users where id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result){
            echo json_encode($result);
        }else {
            echo json_encode(["message" => "Users not found"]);
        }
    }else{
        $stmt = $pdo->query("select * from Users");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }
}

function createUsers() {
    global $pdo;
    
    // Get JSON input data
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if required fields exist
    if (!isset($data['name'], $data['email'], $data['password'], $data['phone'], $data['address'])) {
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    // Validate password length
    $password = trim($data['password']);
    if (strlen($password) < 4) {
        echo json_encode(["error" => "Password must be at least 4 characters"]);
        exit;
    }

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    if (!$hashedPassword) {
        echo json_encode(["error" => "Password hashing failed"]);
        exit;
    }

    // Prepare SQL statement
    $stmt = $pdo->prepare("INSERT INTO Users (name, email, password, phone, address,role) 
                           VALUES (:name, :email, :password, :phone, :address,:role)");

    // Execute query with data
    $success = $stmt->execute([
        ':name'     => $data['name'],
        ':email'    => $data['email'],
        ':password' => $hashedPassword,
        ':phone'    => $data['phone'],
        ':address'  => $data['address'],
        ':role'  => $data['role']
    ]);
    if ($success) {
        echo json_encode(['message' => "User created successfully"]);
    } else {
        echo json_encode(['error' => "Failed to create user"]);
    }
}


function updateUsers(){
    global $pdo;
    $id = $_GET['id'];
    $data = json_decode(file_get_contents('php://input'), true);

    $stmt = $pdo->prepare("update Users set name = :name,role = :role where id = :id");
    if($stmt->execute([
        ":name" => $data['name'],
        ":role" => $data['role'],
        ":id" => $id
    ])){
        echo json_encode(['message' => "Users updated successfully"]);
    }else{
        echo json_encode(['message' => "Unable to update Users"]);
    }
}

function deleteUsers(){
    global $pdo;
    $id = $_GET['id'];

    $stmt = $pdo->prepare("update Users set active = 0 where id = :id");
    if($stmt->execute([':id' => $id])){
        echo json_encode(['message' => "Users deleted successfully"]);
    } else {
        echo json_encode(['message' => "Unable to delete Users"]);
    }
}
?>
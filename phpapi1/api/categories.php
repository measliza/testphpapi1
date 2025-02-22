<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requeted-With");

include "../connect.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getCategories();
        break;
    case 'POST':
        if(isset($_GET['id'])){
            updateCategories();
        }else{
            createCategories();
        }
        break;
    case 'PUT':
        deleteCategories();
        break;
    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

function getCategories(){
    global $pdo;
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $stmt = $pdo->prepare("select * from Categories where id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result){
            echo json_encode($result);
        }else {
            echo json_encode(["message" => "Category not found"]);
        }
    }else{
        $stmt = $pdo->query("select * from Categories");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }
}

function createCategories(){
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);

    $stmt = $pdo->prepare("insert into Categories (name) values (:name)");
    if($stmt->execute([
        ':name' => $data['name'],
    ])){
        echo json_encode(['message' => "Category created successfully"]);
    }else {
        echo json_encode(['message' => "Unable to create category"]);
    }
}

function updateCategories(){
    global $pdo;
    $id = $_GET['id'];
    $data = json_decode(file_get_contents('php://input'), true);

    $stmt = $pdo->prepare("update Categories set name = :name where id = :id");
    if($stmt->execute([
        ":name" => $data['name'],
        ":id" => $id
    ])){
        echo json_encode(['message' => "Category updated successfully"]);
    }else{
        echo json_encode(['message' => "Unable to update category"]);
    }
}

function deleteCategories(){
    global $pdo;
    $id = $_GET['id'];

    $stmt = $pdo->prepare("update Categories set active = 0 where id = :id");
    if($stmt->execute([':id' => $id])){
        echo json_encode(['message' => "Product deleted successfully"]);
    } else {
        echo json_encode(['message' => "Unable to delete product"]);
    }
}
?>
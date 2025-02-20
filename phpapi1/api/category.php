<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include "../connect.php";

    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){
        case 'GET':
            getCategory();
            break;
        case 'POST':
            createCategory();
            break;
        case 'PUT':
            updateCategory();
            break;
        default:
            echo json_encode(["message" => "Invalid request method"]);
            break;
    }

    function getCategory(){
        global $pdo;
        if(isset($_GET['id'])){
            $id = intval($_GET['id']); // ✅ Ensure ID is an integer
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if($result){
                echo json_encode($result);
            } else {
                echo json_encode(["message" => "Category not found"]);
            }
        } else {
            $stmt = $pdo->query("SELECT * FROM categories");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($result);
        }
    }

    function createCategory() {
        global $pdo;

        // ✅ Fix: Decode JSON as an associative array
        $data = json_decode(file_get_contents("php://input"), true);

        // ✅ Check if 'name' is provided
        if (!isset($data['name']) || empty(trim($data['name']))) {
            echo json_encode(["message" => "Category name is required"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
            $stmt->execute([':name' => trim($data['name'])]);

            echo json_encode(["message" => "Category created successfully"]);
        } catch (Exception $e) {
            echo json_encode(["message" => "Error: " . $e->getMessage()]);
        }
    }



    function updateCategory() {
       global $pdo;
       $id = $_GET['id'];
       $data = json_decode(file_get_contents('php://input'),true);

       $stmt = $pdo->prepare("Update categories set name = :name where id = :id");
       if($stmt->execute([
         ":name" => $data['name'],
         ":id" => $id
       ])){
         echo json_encode(["message" => "Category updated successfully"]);
       }else
        echo json_encode(["message" => "Error updating category"]);
    }

    
?>

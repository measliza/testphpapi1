<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include "../connect.php";

    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){
        case 'GET':
            getProducts();
            break;
        case 'POST':
            createProducts();
            break;
        // case 'PUT':
        //     updateCategory();
        //     break;
        default:
            echo json_encode(["message" => "Invalid request method"]);
            break;
    }

    function getProducts(){
        global $pdo;
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $stmt = $pdo->prepare("
                select products.*, categories.name as categories_name
                from products
                let join categories on products.categories_id = categories.id
                where productid = :id
            ");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if($result){
                echo json_encode($result);
            } else {
                echo json_encode(["message" => "Product not found"]);
            }
        }else{
            $stmt = $pdo->prepare("
                select products.*, categories.name as categories_name
                from products
                let join categories on products.categories_id = categories.id    
            ");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if($result){
                echo json_encode($result);
            } else {
                echo json_encode(["message" => "Product not found"]);
            }
        }
    }
    function createProducts() {
        global $pdo;
        $uploadDir = "../uploads/";
    
        // Ensure upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
    
        // Handle file upload safely
        $fileName = "";
        if (!empty($_FILES['image']['name'])) {
            $fileName = basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $fileName;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);
        }
    
        // Corrected SQL syntax and parameter naming
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, category_id, image, active) 
            VALUES (:name, :description, :price, :stock, :category_id, :image, :active)");
    
        try {
            if ($stmt->execute([
                ':name' => $_POST['name'],
                ':description' => $_POST['description'],
                ':price' => $_POST['price'],
                ':stock' => $_POST['stock'],
                ':category_id' => $_POST['category_id'],  
                ':image' => $fileName,
                ':active' => 1
            ])) {
                echo json_encode(["message" => "Product created successfully"]);
            } else {
                echo json_encode(["error" => "Unable to create product"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    
    
?>
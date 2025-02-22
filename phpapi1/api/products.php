<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requeted-With");

include "../connect.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getProducts();
        break;
    case 'POST':
        if(isset($_GET['id'])){
            updateProducts();
        }else{
            createProducts();
        }
        break;
    case 'PUT':
        deleteProducts();
        break;
    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

function getProducts(){
    global $pdo;
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $stmt = $pdo->prepare("
            select
                products.*,
                categories.name as categories_name,
                concat('http://localhost/test/phpapi1/uploads/', products.image) as image_url
            from products
            left join categories on products.category_id = categories.id
            where products.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result){
            echo json_encode($result);
        } else {
            echo json_encode(["message" => "Product not found"]);
        }
    }else{
        $stmt = $pdo->query("
            select
                products.*,
                categories.name as categories_name,
                concat('http://localhost/test/phpapi1/uploads/', products.image) as image_url
            from products
            left join categories on products.category_id = categories.id
        ");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(["message" => "Product not found"]);
        }
    }
}

function createProducts(){
    global $pdo;

    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $fileName = basename($_FILES['image']['name']);
    $uploadFile = $uploadDir. $fileName;
    move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);

    $stmtOrder = $pdo->prepare("select max(`order`) as max_order from products");
    $stmtOrder->execute();
    $order = ($stmtOrder->fetch(PDO::FETCH_ASSOC)['max_order'] ?? 0) + 1;

    $stmt = $pdo->prepare("insert into products (name, image, active, `order`, category_id) values (:name, :image, :active, :order, :categories_id)");
    if($stmt->execute([
        ':name' => $_POST['name'],
        ':image' => $fileName,
        ':active' => 1,
        ':order' => $order,
        ':categories_id' => $_POST['category_id']
    ])){
        echo json_encode(['message' => "Product created successfully"]);
    }else {
        echo json_encode(['message' => "Unable to create product"]);
    }
}

function updateProducts(){
    global $pdo;
    $id = $_GET['id'];

    $uploadDir = "../uploads/";

    $fileName = basename($_FILES['image']['name']);
    $uploadFile = $uploadDir . $fileName;
    move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);

    $stmtOrder = $pdo->prepare("select `order` as max_order from products");
    $stmtOrder->execute();
    $order = ($stmtOrder->fetch(PDO::FETCH_ASSOC)['max_order'] ?? 0) + 1;

    $stmt = $pdo->prepare("update Coupons set
                            id = :id,
                            image = :image,
                            active = :active,
                            `order` = :order,
                            category_id = :categories_id
                            where id = :id"
                        );
    if ($stmt->execute([
        ':name' => $_POST['name'],
        ':image' => $fileName,
        ':active' => 1,
        ':order' => $order,
        ':categories_id' => $_POST['category_id'],
        ':id' => $id,
    ])) {
        echo json_encode(['message' => "Product updated successfully"]);
    } else {
        echo json_encode(['message' => "Unable to update product"]);
    }
}

function deleteProducts(){
    global $pdo;
    $id = $_GET['id'];

    $stmt = $pdo->prepare("update products set active = 0 where id = :id");
    if($stmt->execute([':id' => $id])){
        echo json_encode(['message' => "Product deleted successfully"]);
    } else {
        echo json_encode(['message' => "Unable to delete product"]);
    }
}

?>
<?php
$host = "localhost";
$username = "root";  // Change if using another user
$password = "";      // Change if your MySQL has a password
$dbname = "phpapi1";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable error handling
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die(json_encode(["message" => "Database connection failed: " . $e->getMessage()]));
}
?>

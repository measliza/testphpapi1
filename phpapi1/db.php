<?php
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "phpapi1";

    try {
        // Create PDO connection (without specifying the database)
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable error mode

        // Check if database exists before creating
        $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
        $pdo->exec($sql);

        echo "Database '$dbname' created successfully (or already exists).";

    } catch (PDOException $e) {
        die("Connection Failed: " . $e->getMessage());
    }
?>

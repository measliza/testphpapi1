<?php
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "phpapi1";

    try {
        // Create PDO connection
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable error mode

        // Create categories table FIRST
        $sqlCategory = "CREATE TABLE IF NOT EXISTS category (
            id INTEGER AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB";
        $pdo->query($sqlCategory);

        // Create users table
        $sqlUsers = "CREATE TABLE IF NOT EXISTS users (
            id INTEGER AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password TEXT NOT NULL,
            phone VARCHAR(20) NULL,
            address TEXT NULL,
            role VARCHAR(20) DEFAULT 'customer_admin_admin',
            active TINYINT DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB";
        $pdo->query($sqlUsers);

        // Create products table (after categories)
        $sqlProducts = "CREATE TABLE IF NOT EXISTS products (
            id INTEGER AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            price DECIMAL(10,2) NOT NULL,
            stock INTEGER NULL,
            category_id INTEGER,
            image VARCHAR(255) NULL,
            active INTEGER DEFAULT 1,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        ) ENGINE=InnoDB";
        $pdo->query($sqlProducts);

        echo "Tables created successfully.";

    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
?>

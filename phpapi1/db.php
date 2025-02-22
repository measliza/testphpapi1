<?php
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "phpapi1";

    $pdo = new PDO("mysql:host=$host", $username, $password);
    try{
        $sql = "create database $dbname";

        $pdo->exec($sql);
        echo "Database created successfully";
    }catch(Exception $e){
        die("Connetion Failed:". $e->getMessage());
    }
?>
<?php
$host = "localhost";
$username = "root"; // default XAMPP username
$password = ""; // default XAMPP password
$database = "app_dev"; // make sure this matches your database name

try {
    $connect_database = new mysqli($host, $username, $password, $database);
    
    // Check connection
    if ($connect_database->connect_error) {
        throw new Exception("Connection failed: " . $connect_database->connect_error);
    }
} catch (Exception $e) {
    die("Connection error: " . $e->getMessage());
}
?>
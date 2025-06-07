<?php
$host = "localhost";
$dbname = "vietnghe_keychain";
$username = "root";
$password = "";

try {
    // Create connection using mysqli
    $conn = new mysqli($host, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset
    $conn->set_charset("utf8mb4");
    
    // Make the connection available globally
    $GLOBALS['db'] = $conn;
    
} catch(Exception $e) {
    die("Connection failed: " . $e->getMessage());
} 
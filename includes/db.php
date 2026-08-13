<?php
// Database configuration
$host = trim(getenv('DB_HOST') ?: 'localhost'); 
$dbname = trim(getenv('DB_NAME') ?: 'apnaaghar_db'); 
$username = trim(getenv('DB_USER') ?: 'root'); 
$password = trim(getenv('DB_PASS') ?: ''); 
$port = trim(getenv('DB_PORT') ?: '3306');

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch associative arrays by default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("ERROR: Could not connect to the database. " . $e->getMessage());
}
?>

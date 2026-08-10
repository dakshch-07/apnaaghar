<?php
// Database configuration
// For local development: use localhost, apnaaghar_db, root, and empty password.
// For production (InfinityFree): replace with your cPanel MySQL details.
$host = 'sql209.infinityfree.com'; 
$dbname = 'if0_42621643_apnaaghar_db'; 
$username = 'rootif0_42621643'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch associative arrays by default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("ERROR: Could not connect to the database. " . $e->getMessage());
}
?>

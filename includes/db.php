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

    // Auto-migration for images_json column
    try {
        $pdo->exec("ALTER TABLE properties ADD COLUMN images_json LONGTEXT NULL AFTER image_url");
    } catch(PDOException $e) {
        // Ignore duplicate column errors or other errors during auto-migration
    }

    // Auto-migration for description column
    try {
        $pdo->exec("ALTER TABLE properties ADD COLUMN description LONGTEXT NULL AFTER size");
    } catch(PDOException $e) {
        // Ignore duplicate column errors or other errors during auto-migration
    }

} catch(PDOException $e) {
    die("ERROR: Could not connect to the database. " . $e->getMessage());
}
?>

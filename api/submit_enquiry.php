<?php
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

// Ensure the table exists
$tableSql = "CREATE TABLE IF NOT EXISTS enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(255),
    property_type VARCHAR(255),
    budget VARCHAR(255),
    message TEXT,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$pdo->exec($tableSql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $property = $_POST['interested_property'] ?? '';
    $budget = $_POST['budget'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($name) || empty($phone)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Name and Phone are required."]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO enquiries (name, phone, email, property_type, budget, message) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $phone, $email, $property, $budget, $message])) {
        http_response_code(200);
        echo json_encode(["status" => "success"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database insert failed."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
}
?>

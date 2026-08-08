<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Property ID is required']);
    exit;
}

$id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
    $stmt->execute([$id]);
    $prop = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($prop) {
        // Decode features if it's stored as JSON
        if (!empty($prop['features'])) {
            $decoded = json_decode($prop['features'], true);
            $prop['features'] = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [$prop['features']];
        } else {
            $prop['features'] = [];
        }
        
        // Return success
        echo json_encode(['success' => true, 'data' => $prop]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Property not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}

<?php
require_once 'includes/db.php';
$stmt = $pdo->query('SELECT * FROM admin_users');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

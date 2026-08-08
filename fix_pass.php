<?php
require_once 'includes/db.php';
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE username = 'admin@apnaghar'");
$stmt->execute([$hash]);
echo "Password reset to admin123";

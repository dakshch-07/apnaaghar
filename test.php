<?php require 'includes/db.php'; print_r($pdo->query('SELECT location, type, bhk FROM properties')->fetchAll(PDO::FETCH_ASSOC));

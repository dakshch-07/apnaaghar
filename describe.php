<?php
require "includes/db.php";
try {
    $stmt = $pdo->query("DESCRIBE properties");
    $cols = $stmt->fetchAll();
    foreach($cols as $col) {
        echo $col["Field"] . "<br>";
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>

<?php require "includes/db.php"; try { $pdo->exec("ALTER TABLE properties ADD COLUMN description TEXT NULL AFTER size"); echo "Success"; } catch(Exception $e) { echo $e->getMessage(); } ?>

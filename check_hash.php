<?php
$hash = '$2y$10$tZ9y1vT/x5v.0Qy6H7kMJu.x6w.N8Q.2m9T8yY4.yPzN/0xT7W7';
$password = 'admin123';
if (password_verify($password, $hash)) {
    echo "Match";
} else {
    echo "NO MATCH";
}

<?php
session_start();
$_SESSION = array();
session_destroy();
setcookie('admin_auth', '', time() - 3600, '/');
header("Location: login.php");
exit;
?>

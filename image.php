<?php
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    exit;
}

$id = (int)$_GET['id'];
$idx = isset($_GET['idx']) ? (int)$_GET['idx'] : -1;

$stmt = $pdo->prepare("SELECT image_url, images_json FROM properties WHERE id = ?");
$stmt->execute([$id]);
$prop = $stmt->fetch();

if (!$prop) {
    http_response_code(404);
    exit;
}

$target_data = '';

if ($idx === -1) {
    $target_data = $prop['image_url'];
} else {
    $images = json_decode($prop['images_json'], true);
    if (is_array($images) && isset($images[$idx])) {
        $target_data = $images[$idx];
    }
}

if (empty($target_data)) {
    http_response_code(404);
    exit;
}

// If it's a legacy URL (e.g., uploads/...) or external URL, redirect
if (strpos($target_data, 'http') === 0 || strpos($target_data, 'uploads/') === 0) {
    $url = strpos($target_data, 'uploads/') === 0 ? '/' . $target_data : $target_data;
    header("Location: " . $url);
    exit;
}

// Parse base64 data URI using string functions to avoid PCRE backtrack limits on large strings
if (strpos($target_data, 'data:image/') === 0 && strpos($target_data, ';base64,') !== false) {
    list($meta, $base64) = explode(';base64,', $target_data, 2);
    $mime = str_replace('data:', '', $meta);
    
    // Enable caching for 30 days to save Vercel bandwidth/compute
    $etag = md5($target_data);
    header("Cache-Control: public, max-age=2592000");
    header("Etag: $etag");
    
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
        header("HTTP/1.1 304 Not Modified");
        exit;
    }
    
    header("Content-Type: $mime");
    echo base64_decode($base64);
    exit;
}

http_response_code(404);
?>

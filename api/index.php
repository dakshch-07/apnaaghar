<?php
// Get the requested URI path
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Clean up slash at beginning
$path = ltrim($requestUri, '/');

// If accessing the root, serve index.php
if ($path === '') {
    $path = 'index.php';
}

$file = dirname(__DIR__) . '/' . $path;

// 1. If it's a directory and doesn't end with a slash, redirect with trailing slash (avoids broken relative paths)
if (is_dir($file) && substr($requestUri, -1) !== '/') {
    header("Location: " . $requestUri . "/");
    exit;
}

// 2. If it's a directory, check for default index files (like index.php, dashboard.php)
if (is_dir($file)) {
    $indexFiles = ['index.php', 'index.html', 'dashboard.php'];
    foreach ($indexFiles as $index) {
        $checkFile = rtrim($file, '/') . '/' . $index;
        if (file_exists($checkFile)) {
            $file = $checkFile;
            break;
        }
    }
}

// Prevent directory traversal attacks
$realPath = realpath($file);
$baseDir = dirname(__DIR__);

if ($realPath && strpos($realPath, $baseDir) === 0 && is_file($realPath)) {
    // If it's a PHP file, execute it
    if (pathinfo($realPath, PATHINFO_EXTENSION) === 'php') {
        // Adjust the working directory so relative file lookups work correctly
        chdir(dirname($realPath));
        require $realPath;
        exit;
    } else {
        // Fallback for static files
        header('Content-Type: ' . mime_content_type($realPath));
        readfile($realPath);
        exit;
    }
}

// File not found
http_response_code(404);
echo "404 Not Found";
?>

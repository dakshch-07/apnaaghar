<?php
header('Content-Type: text/plain');

$rawHost = getenv('DB_HOST');
$host = trim($rawHost);
echo "1. Raw Host value hex: " . bin2hex($rawHost) . "\n";
echo "2. Trimmed Host value: [" . $host . "]\n";
echo "3. Trimmed Character count: " . strlen($host) . "\n";

if (!$host) {
    echo "Error: DB_HOST environment variable is not set on Vercel!\n";
    exit;
}

echo "4. Resolving hostname using gethostbyname...\n";
$ip = gethostbyname($host);
if ($ip === $host) {
    echo "DNS Resolution Failed: could not resolve $host\n";
} else {
    echo "DNS Resolution Succeeded: $host resolves to $ip\n";
}

echo "5. Testing raw socket connection on port 15978...\n";
$connection = @fsockopen($host, 15978, $errno, $errstr, 5);
if (is_resource($connection)) {
    echo "Socket Connection Succeeded!\n";
    fclose($connection);
} else {
    echo "Socket Connection Failed: [$errno] $errstr\n";
}
?>

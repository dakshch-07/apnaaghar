<?php
$dir = new RecursiveDirectoryIterator(__DIR__);
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile() && in_array($file->getExtension(), ["php", "html", "js", "css"])) {
        $content = file_get_contents($file->getPathname());
        $newContent = str_ireplace("Apnaa Ghar Real Estate & Interior", "Apnaa Ghar Real Estate & Interior", $content);
        $newContent = str_ireplace("Apnaa Ghar Real Estate & Interior", "Apnaa Ghar Real Estate & Interior", $newContent);
        if ($newContent !== $content) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Fixed: " . $file->getPathname() . "\n";
        }
    }
}
?>

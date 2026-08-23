<?php
$dir = new RecursiveDirectoryIterator(__DIR__);
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile() && in_array($file->getExtension(), ["php", "html", "js", "css"])) {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, "Apnaa Ghar Real Estate & Interior") !== false) {
             // First change "Apnaa Ghar Real Estate & Interior" to "Apnaa Ghar Real Estate & Interior" so we do not end up with "Apnaa Ghar Real Estate & Interior & Interior"
             $content = str_ireplace("Apnaa Ghar Real Estate & Interior", "Apnaa Ghar Real Estate & Interior", $content);
        }
        $newContent = str_ireplace("Apnaa Ghar Real Estate & Interior", "Apnaa Ghar Real Estate & Interior & Interior", $content);
        if ($newContent !== $content) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Updated: " . $file->getPathname() . "\n";
        }
    }
}
?>

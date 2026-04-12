<?php
include_once('common.php');


$config = getProjectConfig();

if (!$config) {
    die("Invalid or missing config.\n");
}

$baseRemoteUrl = $config['base_url'];
$project_dir   = $config['project_dir'];

$htmlDir = $baseDir . '/' . $project_dir;

/**
 * Normalize path (reuse your logic)
 */

/**
 * Get all HTML files (no subfolders)
 */
$htmlFiles = glob($htmlDir . '/*.html');

foreach ($htmlFiles as $htmlFile) {

    $content = file_get_contents($htmlFile);

    /**
     * Fix CSS paths
     */
    $content = preg_replace_callback(
        '/<link[^>]+href=["\']([^"\']+)["\']/i',
        function ($matches) {
            $url = $matches[1];

            // Skip absolute URLs
            if (preg_match('/^(https?:|\/\/|data:)/i', $url)) {
                return $matches[0];
            }

            // Only CSS files
            if (!preg_match('/\.css$/i', $url)) {
                return $matches[0];
            }

            $fileName = basename($url);

            // Force correct path
            $newPath = 'css/' . $fileName;

            return str_replace($url, $newPath, $matches[0]);
        },
        $content
    );

    /**
     * Fix JS paths
     */
    $content = preg_replace_callback(
        '/<script[^>]+src=["\']([^"\']+)["\']/i',
        function ($matches) {
            $url = $matches[1];

            // Skip absolute URLs
            if (preg_match('/^(https?:|\/\/|data:)/i', $url)) {
                return $matches[0];
            }

            // Only JS files
            if (!preg_match('/\.js$/i', $url)) {
                return $matches[0];
            }

            $fileName = basename($url);

            // Force correct path
            $newPath = 'js/' . $fileName;

            return str_replace($url, $newPath, $matches[0]);
        },
        $content
    );

    // Save updated HTML
    file_put_contents($htmlFile, $content);

    echo "Fixed: " . basename($htmlFile) . PHP_EOL;
}

echo "All HTML files updated.\n";
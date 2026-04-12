<?php
include_once('common.php');


$config = getProjectConfig();

if (!$config) {
    die("Invalid or missing config.\n");
}

$baseRemoteUrl = $config['base_url'];
$project_dir   = $config['project_dir'];
$cssDir  = $basedir . '' . $project_dir . '/css';

/**
 * Scan CSS files
 */
$cssFiles = glob($cssDir . '/*.css');

foreach ($cssFiles as $cssFilePath) {

    $cssContent = file_get_contents($cssFilePath);

    // Remove comments
    $cssContent = preg_replace('!/\*.*?\*/!s', '', $cssContent);

    // Extract all url(...)
    preg_match_all('/url\(\s*([^\)]+)\s*\)/i', $cssContent, $matches);

    if (empty($matches[1])) continue;

    foreach ($matches[1] as $rawUrl) {
            // Replaced
        $url = trim($rawUrl, " \t\n\r\0\x0B\"'");

            // remove ?v= and #iefix
        $url = preg_replace('/[\?#].*/', '', $url);

        // Skip absolute/data URLs
        if (preg_match('/^(https?:|data:|\/\/)/i', $url)) {
            continue;
        }

        //  Only FONT files
        if (!preg_match('/\.(woff2?|ttf|otf|eot|svg)(\?.*)?$/i', $url)) {
            continue;
        }

        // Current CSS directory
        $cssDirPath = dirname($cssFilePath);

        // Relative path
        $relativeCssDir = str_replace($basedir, '', $cssDirPath);
        $relativeCssDir = trim(str_replace('\\', '/', $relativeCssDir), '/');

        // Combine + normalize
        $combinedPath = $relativeCssDir . '/' . $url;
        $normalizedPath = normalizePath($combinedPath);

                                                                                           // Replaced
        // Remove project_dir duplication
        $normalizedPath = str_replace($project_dir . '/', '', $normalizedPath);

        // Final correct URL
        $remoteUrl = rtrim($baseRemoteUrl, '/') . '/' . ltrim($normalizedPath, '/');
        // Local save path
        $fileName = basename(parse_url($normalizedPath, PHP_URL_PATH));

        // Save inside css/fonts/
        // Replaced
        $localDir = $cssDir;

        if (!is_dir($localDir)) {
            mkdir($localDir, 0755, true);
        }

        $localFilePath = $localDir . '/' . $fileName;
        pree( $localFilePath);
        // Skip if exists
        if (file_exists($localFilePath)) {
            continue;
        }

        // Download font
        $fileData = @file_get_contents($remoteUrl);

        if ($fileData !== false) {
            file_put_contents($localFilePath, $fileData);
            echo "Downloaded font: " . $fileName . PHP_EOL;
        } else { 
            // Replaced
            echo "Trying: " . $remoteUrl . PHP_EOL;
        }
    }
}

echo "Fonts cloning done.\n";
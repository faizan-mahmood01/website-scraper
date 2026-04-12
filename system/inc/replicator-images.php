<?php
include_once('common.php');



$config = getProjectConfig();

if (!$config) {
    die("Invalid or missing config.\n");
}

$baseRemoteUrl = rtrim($config['base_url'], '/');
$project_dir   = $config['project_dir'];
$projectPath   = $basedir . '/' . $project_dir;

// Allowed image extensions
$imageExtensions = ['jpg','jpeg','png','gif','webp','svg','ico','bmp'];

// Prevent duplicate downloads
$downloaded = [];

// Scan project files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($projectPath, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {

    if (!$file->isFile()) continue;

    $filePath = $file->getPathname();

    $content = @file_get_contents($filePath);
    if ($content === false) continue;

    preg_match_all('/(?:src|data-src)\s*=\s*[\"\']([^\"\']+)[\"\']|url\(\s*([^)]+)\s*\)/i', $content, $matches);

    $urls = array_merge($matches[1] ?? [], $matches[2] ?? []);

    foreach ($urls as $rawUrl) {

        $url = trim($rawUrl, " \t\n\r\0\x0B\"'");

        if (!$url) continue;

        // Skip base64 & external
        if (preg_match('/^data:/i', $url)) continue;
        if (preg_match('/^(https?:)?\/\//i', $url)) continue;

        // Clean URL
        $url = strtok($url, '#');
        $url = strtok($url, '?');

        // Extension check
        $path = parse_url($url, PHP_URL_PATH);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, $imageExtensions)) continue;

        // Resolve path correctly
        if (strpos($url, '/') === 0) {
            // Root-relative → directly from project root
            $normalizedPath = ltrim($url, '/');
        } else {
            // Relative → based on current file
            $relativeDir = str_replace($basedir, '', dirname($filePath));
            $relativeDir = trim(str_replace('\\', '/', $relativeDir), '/');

            $combinedPath = $relativeDir . '/' . $url;
            $normalizedPath = normalizePath($combinedPath);
        }

        // Clean path again
        $cleanPath = parse_url($normalizedPath, PHP_URL_PATH);

        // Avoid duplicates
        if (isset($downloaded[$cleanPath])) continue;
        $downloaded[$cleanPath] = true;

        // Build remote URL
        $remoteUrl = $baseRemoteUrl . '/' . $cleanPath;

        // Fix duplicate project_dir issue
        $remoteUrl = replaceNthOccurrence($project_dir . '/', '', $remoteUrl, 2);

        // Local path
        $localFilePath = $basedir . '/' . $cleanPath;

        // Ensure directory
        $localDir = dirname($localFilePath);
        if (!is_dir($localDir)) {
            mkdir($localDir, 0755, true);
        }

        if (file_exists($localFilePath)) continue;

        // Download silently
        $fileData = @file_get_contents($remoteUrl);
          pree($fileData);
        if ($fileData !== false) {
            file_put_contents($localFilePath, $fileData);
        }
    }
}

echo "Images cloning .\n";

            // Next Working Code

// function runImageCloner() {

//     $basedir = __DIR__;
//     $config = getProjectConfig();

//     if (!$config) {
//         return "Invalid or missing config.";
//     }

//     $baseRemoteUrl = rtrim($config['base_url'], '/');
//     $project_dir   = $config['project_dir'];
//     $projectPath   = $basedir . '/' . $project_dir;

//     $imageExtensions = ['jpg','jpeg','png','gif','webp','svg','ico','bmp'];
//     $downloaded = [];

//     $iterator = new RecursiveIteratorIterator(
//         new RecursiveDirectoryIterator($projectPath, RecursiveDirectoryIterator::SKIP_DOTS)
//     );

//     foreach ($iterator as $file) {

//         if (!$file->isFile()) continue;

//         $filePath = $file->getPathname();
//         $content = @file_get_contents($filePath);
//         if ($content === false) continue;

//         preg_match_all('/(?:src|data-src)\s*=\s*[\"\']([^\"\']+)[\"\']|url\(\s*([^)]+)\s*\)/i', $content, $matches);

//         $urls = array_merge($matches[1] ?? [], $matches[2] ?? []);

//         foreach ($urls as $rawUrl) {

//             $url = trim($rawUrl, " \t\n\r\0\x0B\"'");
//             if (!$url) continue;

//             if (preg_match('/^data:/i', $url)) continue;
//             if (preg_match('/^(https?:)?\/\//i', $url)) continue;

//             $url = strtok($url, '#');
//             $url = strtok($url, '?');

//             $path = parse_url($url, PHP_URL_PATH);
//             $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
//             if (!in_array($ext, $imageExtensions)) continue;

//             if (strpos($url, '/') === 0) {
//                 $normalizedPath = ltrim($url, '/');
//             } else {
//                 $relativeDir = str_replace($basedir, '', dirname($filePath));
//                 $relativeDir = trim(str_replace('\\', '/', $relativeDir), '/');

//                 $combinedPath = $relativeDir . '/' . $url;
//                 $normalizedPath = normalizePath($combinedPath);
//             }

//             $cleanPath = parse_url($normalizedPath, PHP_URL_PATH);

//             if (isset($downloaded[$cleanPath])) continue;
//             $downloaded[$cleanPath] = true;

//             $remoteUrl = $baseRemoteUrl . '/' . $cleanPath;
//             $remoteUrl = replaceNthOccurrence($project_dir . '/', '', $remoteUrl, 2);

//             $localFilePath = $basedir . '/' . $cleanPath;

//             $localDir = dirname($localFilePath);
//             if (!is_dir($localDir)) {
//                 mkdir($localDir, 0755, true);
//             }

//             if (file_exists($localFilePath)) continue;

//             $fileData = @file_get_contents($remoteUrl);

//             if ($fileData !== false) {
//                 file_put_contents($localFilePath, $fileData);
//             }
//         }
//     }

//     return "Images cloned successfully!";
// }

// // If called via AJAX
// if (isset($_POST['run_cloner'])) {
//     echo runImageCloner();
// }
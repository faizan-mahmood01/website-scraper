<?php
include_once('common.php');



$config = getProjectConfig();

if (!$config) {
    die("Invalid or missing config.\n");
}

$baseRemoteUrl = $config['base_url'];
$project_dir   = $config['project_dir'];
$cssDir = $basedir . '/' . $project_dir . '/css';
$cssFiles = glob($cssDir . "/*.css");

echo json_encode([
    "hasCss" => count($cssFiles) > 0
]);
<?php
include_once('common.php');



$config = getProjectConfig();
// pree($config);
if (!$config) {
    die("Invalid or missing config.\n");
}

$baseRemoteUrl = $config['base_url'];
$project_dir   = $basedir.$config['project_dir'];
$files = glob($project_dir . '/*.html');

echo json_encode([
    "hasHtml" => count($files) > 0
]);
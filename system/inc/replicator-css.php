<?php
include_once('common.php');




$config = getProjectConfig();

if (!$config) {
    die("Invalid or missing config.\n");
}

$baseRemoteUrl = $config['base_url'];
$project_dir   = $basedir.$config['project_dir'];
$cssDir =    $project_dir . '/css';

// Ensure css directory exists
if (!is_dir($cssDir)) {
    mkdir($cssDir, 0755, true);
	// pree($cssDir);
}

// Normalize URL path (resolve ../ and ./)



// Scan CSS files
$cssFiles = glob($cssDir . '/*.css');

foreach ($cssFiles as $cssFilePath) {

    $cssDirPath = dirname($cssFilePath);

    // Get relative path from base dir to css file
    $relativeCssPath = str_replace($basedir, '', $cssDirPath);
    $relativeCssPath = str_replace('\\', '/', $relativeCssPath);
    $relativeCssPath = trim($relativeCssPath, '/');
	
	// pree($cssFilePath);
	// exit;
		
	$cssContent = @file_get_contents($cssFilePath);
	$cssContent = preg_replace('!/\*.*?\*/!s', '', $cssContent);
	
	preg_match_all('/url\(\s*([^\)]+)\s*\)/i', $cssContent, $matches);
	
	$urls = array_map(function($url) {
		return trim($url, " \t\n\r\0\x0B\"'");
	}, $matches[1]);
	
	$cssOnlyUrls = array_filter($urls, function($url) {
		// must be relative AND end with .css
		return !preg_match('/^(https?:|data:|\/\/)/i', $url)
			&& preg_match('/\.css(\?.*)?$/i', $url);
	});
	// pree($cssOnlyUrls);exit;
	
	if (empty($matches[1])) continue;
	
	foreach ($matches[1] as $rawUrl) {
	
		$url = trim($rawUrl, " \t\n\r\0\x0B\"'");
	
		// Skip absolute URLs
		if (preg_match('/^(https?:|data:|\/\/)/i', $url)) {
			continue;
		}
	
		// Resolve relative path against current CSS file directory
		$cssDirPath = dirname($cssFilePath);
	
		// Convert filesystem path to relative path from base
		$relativeCssDir = str_replace($basedir, '', $cssDirPath);
		$relativeCssDir = trim(str_replace('\\', '/', $relativeCssDir), '/');
	
		// Combine and normalize path
		$combinedPath = $relativeCssDir . '/' . $url;
		$normalizedPath = normalizePath($combinedPath);

		// Build remote URL
		$fullUrl = rtrim($baseRemoteUrl, '/') . '/' . $normalizedPath;
		// pree($fullUrl);
		$remoteUrl = replaceNthOccurrence(
			basename($project_dir) . '/',
			'',
			$fullUrl,
			2
		);
		// pree($remoteUrl);

    //   pree(basename($project_dir));exit;
		// pree($remoteUrl);
		// exit;
		// Only process .css files
		if (!preg_match('/\.css(\?.*)?$/i', $normalizedPath)) {
			continue;
		}
	
		$fileName = basename(parse_url($normalizedPath, PHP_URL_PATH));
		$localFilePath =$basedir.$relativeCssDir . '/' . $fileName;
		//pree($relativeCssDir.' - '.$fileName);
		// pree($localFilePath);exit;
	
		// Ensure directory exists locally
		$localDir = dirname($localFilePath);
		if (!is_dir($localDir)) {
			mkdir($localDir, 0755, true);
		}
	
		// Skip if already exists
		if (file_exists($localFilePath)) {
			continue;
		}
		//pree($remoteUrl);continue;
		//pree($remoteUrl.' - '.$localFilePath);exit;
			
		// Download
		$fileData = @file_get_contents($remoteUrl);
		
		// pree($remoteUrl);
		// pree($localFilePath);
		// exit;
	
		if ($fileData !== false) {
			file_put_contents($localFilePath, $fileData);
			//echo "Downloaded: " . $normalizedPath . PHP_EOL;
		} else {
			echo "Failed: " . $remoteUrl . PHP_EOL;
		}
	}

}

echo "Done.\n";

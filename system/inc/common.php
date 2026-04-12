<?php global $configFile, $basedir;
$basedir = dirname(__DIR__, 2).'/';
$configFile = dirname(__DIR__) . '/config/config.txt';

function makeAbsolute($url, $base) {
    if (parse_url($url, PHP_URL_SCHEME) != '') return $url;

    if ($url[0] == '/' ) {
        $parts = parse_url($base);
        return $parts['scheme'].'://'.$parts['host'].$url;
    }

    return rtrim($base,'/').'/'.$url;
}

function fetchHTML($url){
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    $context = stream_context_create([
        "http" => [
            "header" => "User-Agent: PHP URL Parser\r\n"
        ]
    ]);

    return @file_get_contents($url,false,$context);
}
 
function normalizePath($path, $project_dir = '') {

    // Normalize slashes
    $path = str_replace('\\', '/', $path);
    $project_dir = str_replace('\\', '/', $project_dir);

    // Remove project directory prefix if exists
    if ($project_dir && strpos($path, $project_dir) === 0) {
        $path = substr($path, strlen($project_dir));
    }

    $parts = [];

    foreach (explode('/', $path) as $segment) {
        if ($segment === '' || $segment === '.') continue;

        if ($segment === '..') {
            array_pop($parts);
        } else {
            $parts[] = $segment;
        }
    }

    // Rebuild path
    $normalized = implode('/', $parts);

    // Remove any leading slash
    $normalized = ltrim($normalized, '/');

    return $normalized;
}
function getProjectConfig() {
	global $configFile;

    if (!file_exists($configFile)) {
        return false;
    }

    $content = file_get_contents($configFile);

    // Try JSON
    $data = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        // fallback to serialized
        $data = unserialize($content);
        if (!is_array($data)) {
            return false;
        }
    }

    // Validate required keys
    if (empty($data['base_url']) || empty($data['project_dir'])) {
        return false;
    }

    return [
        'base_url'    => rtrim($data['base_url'], '/'),
        'project_dir' => trim($data['project_dir'], '/')
    ];
}
function getProjectDir($url) {
    $host = parse_url($url, PHP_URL_HOST);
    return $host ? $host : '';
}
function replaceNthOccurrence($search, $replace, $subject, $nth = 2) {
    $pos = -1;

    for ($i = 0; $i < $nth; $i++) {
        $pos = strpos($subject, $search, $pos + 1);
        if ($pos === false) {
            return $subject; // not enough occurrences
        }
    }

    return substr_replace($subject, $replace, $pos, strlen($search));
}

	if(!function_exists('pre')){
		function pre($data){
			if(isset($_GET['debug'])){
				pree($data);
			}
		}	 
	} 
		
	if(!function_exists('pree')){
		function pree($data){
				echo '<pre>';
				print_r($data);
				echo '</pre>';	
		
		}	 
	} 
	function getBaseUrl($url) {
		// Remove query/hash
		$url = strtok($url, '?#');
	
		// If ends with file, strip it
		if (preg_match('/\/[^\/]+\.[a-zA-Z0-9]+$/', $url)) {
			return dirname($url) . '/';
		}
	
		return rtrim($url, '/') . '/';
	}

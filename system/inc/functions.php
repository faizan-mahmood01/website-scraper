<?php
include_once('common.php');
// include_once('updater.php');
if(isset($_POST['action']) && $_POST['action'] == "generate_files"){
	

    $data = $_POST['data'];
    $baseDownloadPath = $basedir;

    /* ================================
       HELPER FUNCTIONS
    ================================= */

    function getDomain($url){
        return parse_url($url, PHP_URL_HOST);
    }

    function cleanUrl($url){
        $url = strtok($url, '?'); // remove query
        $url = strtok($url, '#'); // remove anchor
        return $url;
    }

    function isSameDomain($url, $mainDomain){
        return getDomain($url) === $mainDomain;
    }

    function getExtension($url){
        return strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
    }

    function isAllowedFile($url, $type){
        $ext = getExtension($url);

        $allowed = [
            "images" => ['jpg','jpeg','png','gif','webp','svg'],
            "scripts" => ['js'],
            "styles" => ['css'],
            "links" => ['html','php','']
        ];

        return in_array($ext, $allowed[$type]);
    }

    function safeFileName($url, $type){
        $path = parse_url($url, PHP_URL_PATH);
        $name = basename($path);

        if(!$name || $name == "/"){
            return "index.html";
        }

        // remove weird characters
        $name = preg_replace("/[^a-zA-Z0-9.\-_]/", "_", $name);

        return $name;
    }

    function downloadFile($url, $savePath){

        // Skip if already exists
        if(file_exists($savePath)) return;

        $context = stream_context_create([
            "http" => [
                "timeout" => 10,
                "header" => "User-Agent: Mozilla/5.0\r\n"
            ]
        ]);

        $content = file_get_contents($url, false, $context);

        if($content !== false){
            // Limit size (2MB)
            if(strlen($content) > 2 * 1024 * 1024){
                return;
            }

            file_put_contents($savePath, $content);
        }
    }

    function createStructure($basePath){
        $folders = ['images','js','css','others'];

        foreach($folders as $folder){
            if(!file_exists($basePath.$folder)){
                mkdir($basePath.$folder, 0777, true);
            }
        }
    }

    /* ================================
       MAIN PROCESS
    ================================= */

    $mainDomain = null;
    $downloaded = [];
	//pree($data);exit;
    foreach($data as $type => $urls){

        foreach($urls as $url){

            $url = cleanUrl($url);

            // Set main domain once
            if($mainDomain === null){
                $mainDomain = getDomain($url);
            }

            // Skip external domains
            if(!isSameDomain($url, $mainDomain)){
                continue;
            }

            // Skip invalid types
            if(!isAllowedFile($url, $type)){
                continue;
            }

            $domainFolder = str_replace("www.", "", $mainDomain);
            $websitePath = $baseDownloadPath.$domainFolder."/";
            //pree($websitePath);//exit;
            if(!file_exists($websitePath)){
                mkdir($websitePath, 0777, true);
                
            }
            if(file_exists($websitePath)){
                createStructure($websitePath);
            }


            $fileName = safeFileName($url, $type);
            
            // Prevent duplicate names
            $originalName = $fileName;
            $i = 1;

            while(in_array($fileName, $downloaded)){
                $fileName = $i . "_" . $originalName;
                $i++;
            }

            $downloaded[] = $fileName;

            switch($type){
                case "images":
                    $savePath = $websitePath."images/".$fileName;
                    break;

                case "scripts":
                    $savePath = $websitePath."js/".$fileName;
                    break;

                case "styles":
                    $savePath = $websitePath."css/".$fileName;
                    break;

                case "links":
                    $savePath = $websitePath."".$fileName;
                    break;

                default:
                    continue 2;
            }
			//pree($url);pree($savePath);exit;
			
            downloadFile($url, $savePath);
            // add my updater file here

            // if($type == "styles"){
            //     fetchCSSImports($savePath, dirname($url));
            // }
        }
    }

    echo json_encode(["status"=>"success"]);
}
<?php include_once('system/inc/common.php');
libxml_use_internal_errors(true);


$images=[];
$links=[];
$scripts=[];
$styles=[];
//  This is the normalized space that  file is placed in common.php

if(isset($_POST['url'])){

$url=$_POST['url'];
$html=fetchHTML($url);
$baseUrl = getBaseUrl($url);
// fahad bahi code 

$existingData = [];
if (file_exists($configFile)) {
    $content = file_get_contents($configFile);

    // Try JSON first
    $decoded = json_decode($content, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $existingData = $decoded;
    } else {
        // fallback to serialized data
        $existingData = unserialize($content);
        if (!is_array($existingData)) {
            $existingData = [];
        }
    }
}

// end here
$projectDir = getProjectDir($baseUrl);
$newData = [
    'base_url'    => rtrim($baseUrl, '/'),
    'project_dir' => $projectDir
    ];
    
   
// Save ONLY if different or empty
if (
    empty($existingData) ||
    !isset($existingData['base_url']) ||
    $existingData['base_url'] !== $newData['base_url']
) {
    file_put_contents($configFile, json_encode($newData, JSON_PRETTY_PRINT));
}

if($html){

$dom=new DOMDocument();
$dom->loadHTML($html);

$xpath=new DOMXPath($dom);

/* Images */
foreach($xpath->query("//img") as $img){
    $src=$img->getAttribute("src");
    $src=makeAbsolute($src,$baseUrl);
    $images[]=$src;
    $allUrls[]=$src;
}

/* Links */
foreach($xpath->query("//a") as $a){
    $href=$a->getAttribute("href");
    if($href){
        $href=makeAbsolute($href,$baseUrl);
        $links[]=$href;
        $allUrls[]=$href;
    }
}

/* Scripts */
foreach($xpath->query("//script") as $script){
    $src=$script->getAttribute("src");
    if($src){
        $src=makeAbsolute($src,$baseUrl);
        $scripts[]=$src;
        $allUrls[]=$src;
    }
}

/* Stylesheets */
foreach($xpath->query("//link[@rel='stylesheet']") as $style){
    $href=$style->getAttribute("href");
    $href=makeAbsolute($href,$baseUrl);
    $styles[]=$href;
    $allUrls[]=$href;
}


/* Remove duplicates */
$images=array_unique($images);
$links=array_unique($links);
$scripts=array_unique($scripts);
$styles=array_unique($styles);


}
else{
$error="Invalid URL or unable to fetch page.";
}

}
?>

<!DOCTYPE html>
<html>
<head>
<title>URL Extractor</title>
  <link href="system/assets/css/bootstrap.min.css" rel="stylesheet">
  <script src="system/assets/js/jquery.js"></script>
<style>
body{font-family:Arial;margin:40px;}
table{border-collapse:collapse;width:100%;margin-bottom:30px;}
td,th{border:1px solid #ccc;padding:8px;}
th{background:#eee;}
</style>
</head>
<body>
        
    <button id="cloneImagesBtn" class="btn btn-success" >
        Clone Images
    </button>

    <!-- CSS Cloner Button -->
    <!-- <button id="cloneCssBtn" class="btn btn-primary me-3">
        Clone CSS
        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
    </button> -->
    <button id="cloneCssBtn" class="btn btn-primary me-3" >
            Clone CSS
    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
    </button>

    <!-- Fonts Cloner Button -->
    <button id="cloneFontsBtn" class="btn btn-success">
        Clone Fonts
        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
    </button>

    <!-- Response Box -->
    <div id="responseBox" class="mt-4 alert d-none"></div>

</div>
<h2>Website URL Extractor</h2>
<!-- For form and button -->
<div class="container mt-4">
    <form method="post" action="">
        <div class="row g-2 align-items-center">
            
            <div class="col-md-8">
                <input 
                    type="text" 
                    name="url" 
                    class="form-control"
                    placeholder="Enter child page URL"
                    value="" style="width:400px;">
            </div>

            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    Fetch Data
                </button>
            </div>

        </div>
    </form>
</div>
<?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>

<?php if(!empty($images)){ ?>
<!-- For Images  -->
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0 section-heading" data-type="images-heading" style="cursor: pointer;">
            Images (<?php echo count($images); ?>)
        </h5>

        <a href="javascript:void(0);" 
           class="btn btn-sm btn-success generate-now" 
           data-type="images">
           Generate File/Folders
        </a>
    </div>

    <div class="table-responsive" data-type="images-div">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Image URL</th>
                </tr>
            </thead>
            <tbody class="gray-body">
                <?php foreach($images as $img){ ?>
                <tr>
                    <td>
                        <input 
                            type="checkbox" 
                            class="form-check-input url-checkbox" 
                            data-type="images" 
                            value="<?php echo $img; ?>" 
                            checked
                        >
                    </td>
                    <td class="text-break">
                        <?php echo $img; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<?php } ?>


<?php if(!empty($links)){ ?>
<!-- For Links -->
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0 section-heading" data-type="links-heading">
            Links (<?php echo count($links); ?>)
        </h5>

        <a href="javascript:void(0);" 
           class="btn btn-sm btn-primary generate-now" 
           data-type="links">
           Generate File/Folders
        </a>
    </div>

    <div class="table-responsive" data-type="links-div">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Link URL</th>
                </tr>
            </thead>
            <tbody class="gray-body">
                <?php foreach($links as $l){ ?>
                <tr>
                    <td>
                        <input 
                            type="checkbox" 
                            class="form-check-input url-checkbox" 
                            data-type="links" 
                            value="<?php echo $l; ?>" 
                            checked
                        >
                    </td>
                    <td class="text-break">
                        <?php echo $l; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<?php } ?>

<?php if(!empty($scripts)){ ?>
<!-- For Scripts -->
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0 section-heading" data-type="scripts-heading">
            Scripts (<?php echo count($scripts); ?>)
        </h5>

        <a href="javascript:void(0);" 
           class="btn btn-sm btn-warning generate-now" 
           data-type="scripts">
           Generate File/Folders
        </a>
    </div>

    <div class="table-responsive" data-type="scripts-div">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Script URL</th>
                </tr>
            </thead>
            <tbody class="gray-body">
                <?php foreach($scripts as $s){ ?>
                <tr>
                    <td>
                        <input 
                            type="checkbox" 
                            class="form-check-input url-checkbox" 
                            data-type="scripts" 
                            value="<?php echo $s; ?>" 
                            checked
                        >
                    </td>
                    <td class="text-break">
                        <?php echo $s; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<?php } ?>

<?php if(!empty($styles)){ ?>
<!-- For Styles -->
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0 section-heading" data-type="styles-heading">
            Stylesheets (<?php echo count($styles); ?>)
        </h5>

        <a href="javascript:void(0);" 
           class="btn btn-sm btn-info generate-now" 
           data-type="styles">
           Generate File/Folders
        </a>
    </div>

    <div class="table-responsive" data-type="styles-div">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Stylesheet URL</th>
                </tr>
            </thead>
            <tbody class="gray-body">
                <?php foreach($styles as $st){ ?>
                <tr>
                    <td>
                        <input 
                            type="checkbox" 
                            class="form-check-input url-checkbox" 
                            data-type="styles" 
                            value="<?php echo $st; ?>" 
                            checked
                        >
                    </td>
                    <td class="text-break">
                        <?php echo $st; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<?php } ?>


<!-- Fahad-Bahi Code -->
<style>
    .generate-now{
        float:right;
        border:1px solid #333;
        padding: 4px;
        cursor:pointer;}
    #cloneImagesBtn{
        /* display:none; */
    }
 .section-heading {
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 5px 10px;        /* space for background */
    border-radius: 5px; 
     background-color: #ffffff83; /* dark background */
     color: #65fd0d;
}

.section-heading:hover {
    color: #65fd0d;
    background-color: #444242;      /* smooth edges */
    transform: scale(1.03);
}
.gray-body {
    background-color: #07070731;
}
.gray-body td {
    background-color: #00000041;
}

.gray-body td:hover {
    background-color: #4e4e4e; /* darker on hover */
}
</style>

<script>
    // Mutation for the CloneCss button Starts here 
 
//  ends here

    // Mutataion starts here
   $(document).ready(function () {

//    To place the toggle button on images
   $("body").on("click", "h5", function () {

    var datatype = $(this).data("type"); // e.g. images-heading

    if (!datatype) return; // safety check

    var div_element = datatype.replace("-heading", "-div");

    $("div[data-type='"+ div_element+"']" ).toggle();

});
    const targetNode = document.body;

    function checkHtmlFiles() {
        $.ajax({
            url: 'system/inc/validator-html.php',
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.hasHtml) {
                    $("#cloneImagesBtn").fadeIn();
                } else {
                    $("#cloneImagesBtn").fadeOut();
                }
            }
        });
    }
      function checkcssFiles() {
        $.ajax({
            url: 'system/inc/validator-css.php',
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.hasCss) {
                    $("#cloneCssBtn").fadeIn();
                } else {
                    $("#cloneCssBtn").fadeOut();
                }
            }
        });
    }

    function checkfontsFiles() {
        $.ajax({
            url: 'system/inc/validator-fonts.php',
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.hasCss) {
                    $("#cloneFontsBtn").fadeIn();
                } else {
                    $("#cloneFontsBtn").fadeOut();
                }
            }
        });
    }

    // Initial check
    checkHtmlFiles();
    checkcssFiles();
    checkfontsFiles();

    // MutationObserver to watch DOM changes
    const observer = new MutationObserver(function (mutationsList, observer) {
        checkHtmlFiles(); // re-check when DOM updates
        checkcssFiles();
        checkfontsFiles();
    });

    observer.observe(targetNode, {
        childList: true,
        subtree: true
    });

    // Optional: also poll every 3 seconds (for backend changes)
    setInterval(checkHtmlFiles, 1000*30);
    setInterval(checkcssFiles, 1000*40);
    setInterval(checkfontsFiles, 1000*50);

});
// ends here

$("#cloneImagesBtn").click(function () {
    $("#cloneStatus").html("Cloning images...");

    $.ajax({
        url: "system/inc/replicator-images.php",
        type: "GET",
        success: function (response) {
            $("#cloneStatus").html(response);
        },
        error: function () {
            $("#cloneStatus").html("Error occurred!");
        }
    });
});

    $(document).ready(function(){

    function handleAjax(button, url) {
        let spinner = button.find(".spinner-border");

        // Start loader
        spinner.removeClass("d-none");
        button.prop("disabled", true);

        $.ajax({
            url: url,
            type: "POST",
            success: function(response){
                $("#responseBox")
                    .removeClass("d-none alert-danger")
                    .addClass("alert-success")
                    .html(response);
            },
            error: function(){
                $("#responseBox")
                    .removeClass("d-none alert-success")
                    .addClass("alert-danger")
                    .html("Something went wrong!");
            },
            complete: function(){
                // Stop loader
                spinner.addClass("d-none");
                button.prop("disabled", false);
            }
        });
    }

    // CSS Cloner Click
    $("#cloneCssBtn").click(function(){
        handleAjax($(this), "system/inc/replicator-css.php");
    });

    // Fonts Cloner Click
    $("#cloneFontsBtn").click(function(){
        handleAjax($(this), "system/inc/replicator-fonts.php");
    });

});
   $(document).on("click", ".generate-now", function(e){

    e.preventDefault();
	
	var dataset_type = $(this).data('type');

    let data = {
        images: [],
        links: [],
        scripts: [],
        styles: []
    };

    $(".url-checkbox:checked[data-type='"+dataset_type+"']").each(function(){
        let type = $(this).data("type");
        let url = $(this).val();

        data[type].push(url);
    });

    // Check if empty
    if(
        data.images.length === 0 &&
        data.links.length === 0 &&
        data.scripts.length === 0 &&
        data.styles.length === 0
    ){
        alert("Please select at least one URL");
        return;
    }

    $.ajax({
        url: "system/inc/functions.php",
        type: "POST",
        data: {
            action: "generate_files",
            data: data
        },
        success: function(response){
            //console.log(response);
            //alert("Files generated successfully");
        },
        error: function(){
            //alert("Something went wrong");
        }
    });

});
</script>
</body>
</html>
<?php
require_once("../includes/main.php");
// find all the css files in the plugin's folder
$css = CrawlCSS($root_path."css/",[]);

function CrawlCSS($path,$css){
    $shared_models_dir = opendir($path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
        if(!is_dir($file) && endsWith($file, '.css') && is_file($path.$file)) { 
            //require_once($path.$file);
            $css[] = $path.$file;
            //echo "included\n";
        } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
            $css = CrawlCSS($path.$file."/",$css);
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    return $css;
}
sort($css);
if(isset($_GET['min'])){
    OutputCSSFromFileListMin($css);
} else {
    OutputCSSFromFileList($css);
}
?>
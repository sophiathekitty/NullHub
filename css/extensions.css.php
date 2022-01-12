<?php
require_once("../includes/main.php");
// find all the css files in the plugin's folder
$css = [];
$extensions = FindLocalExtensions();
foreach($extensions as $extension){
    $css = CrawlExtensionCSS($css,$root_path."extensions/".$extension."/css/");
}

function CrawlExtensionCSS($css,$path){
    //echo "IncludeFolder: $path \n";
    $shared_models_dir = opendir($path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        //echo "<br><i>$file</i> ".is_dir($path.$file)."  ".is_dir($file."/")." <br>";
        // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
        if(!is_dir($file) && endsWith($file, '.css')>0 && is_file($path.$file)) { 
            //echo "Require: $path$file\n";
            //require_once($path.$file);
            //echo "included\n";
            $css[] = $path.$file;
        } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
            $css = CrawlExtensionCSS($css,$path.$file."/");
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    return $css;
}

if(isset($_GET['min'])){
    OutputCSSFromFileListMin($css);
} else {
    OutputCSSFromFileList($css);
}
?>
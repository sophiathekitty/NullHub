<?php
require_once("../includes/main.php");
// find all the css files in the plugin's folder
$js = [];

$plugins = FindPlugins($root_path."plugins/");
foreach($plugins as $plugin){
    $js = CrawlPluginJS($js,$plugin."js/");
}

function CrawlPluginJS($js,$path){
    //echo "IncludeFolder: $path \n";
    $shared_models_dir = opendir($path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        //echo "<br><i>$file</i> ".is_dir($path.$file)."  ".is_dir($file."/")." <br>";
        // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
        if(!is_dir($file) && endsWith($file, '.js')>0 && is_file($path.$file)) { 
            //echo "Require: $path$file\n";
            //require_once($path.$file);
            //echo "included\n";
            $js[] = $path.$file;
        } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
            $js = CrawlPluginJS($js,$path.$file."/");
        }
        
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    return $js;
}

if(isset($_GET['min'])){
    OutputJSFromFileListMin($js);
} else {
    OutputJSFromFileList($js);
}
?>
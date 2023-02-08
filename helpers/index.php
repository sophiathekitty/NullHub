<?php
require_once("../includes/main.php");
// find all the css files in the plugin's folder
$helpers = CrawlHelpers($root_path."helpers/");

function CrawlHelpers($path){
    $helpers = ['quick launch'=>['validate models'=>"http://".LocalIp()."/helpers/validate_models.php",'testing'=>"http://".LocalIp()."/helpers/testing.php?DEBUG=verbose"]];
    $shared_models_dir = opendir($path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
        if(!is_dir($file) && endsWith($file, '.php') && is_file($path.$file)) { 
            //require_once($path.$file);
            if($file != "index.php") $helpers[str_replace("_"," ",str_replace(".php","",$file))] = ["http://".LocalIp()."/helpers/".$file,["http://".LocalIp()."/helpers/".$file."?DEBUG=1","http://".LocalIp()."/helpers/".$file."?DEBUG=verbose"]];
            //echo "included\n";
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    return $helpers;
}
OutputJson($helpers);
?>
<?php

//echo "0.1\n";
//echo "main 01\n";
if(!isset($root_path)){
    $root_path = "";
    $i = 0;
    while(!is_file($root_path."includes/main.php") && $i < 10){
        $root_path .= "../"; $i++;
    }
}

//echo "0.2\n";

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set("America/Denver");

//echo "0.3\n";

IncludeFolder($root_path."includes/utils/");
require_once($root_path."settings.php");
$db = new clsDB($db_info['database'], $db_info['username'], $db_info['password']);

//echo "0.4 $root_path \n";

IncludeFolder($root_path."models/");
IncludeFolder($root_path."modules/");
IncludeFolder($root_path."views/");

//echo "0.5\n";

$plugins = FindPlugins($root_path."plugins/");

//echo "0.6\n";

foreach($plugins as $plugin){
    //echo "0.6.0 $plugin\n";
    IncludeFolder($plugin."models/");
    //echo "0.6.1 $plugin\n";
    IncludeFolder($plugin."modules/"); 
    //echo "0.6.2 $plugin\n";
}

//echo "0.7\n";

function IncludeFolder($path){
    //echo "IncludeFolder: $path \n";
    $shared_models_dir = opendir($path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        //echo "<br><i>$file</i> ".is_dir($path.$file)."  ".is_dir($file."/")." <br>";
        // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
        if(!is_dir($file) && strpos($file, '.php')>0 && is_file($path.$file)) { 
            //echo "Require: $path$file\n";
            require_once($path.$file);
            //echo "included\n";
        } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
            IncludeFolder($path.$file."/");
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
}
function IncludeFolderDebug($path){
    //echo "IncludeFolder: $path \n";
    Settings::SaveSettingsVar("debug-IncludeFolder--path",$path);
    $shared_models_dir = opendir($path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        //echo "<br><i>$file</i> ".is_dir($path.$file)."  ".is_dir($file."/")." <br>";
        // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
        if(!is_dir($file) && strpos($file, '.php')>0 && is_file($path.$file)) { 
            Settings::SaveSettingsVar("debug-IncludeFolder--file",$path.$file);
            //echo "Require: $path$file\n";
            require_once($path.$file);
            Settings::SaveSettingsVar("debug-IncludeFolder--included",$path.$file);
            //echo "included\n";
        } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
            IncludeFolder($path.$file."/");
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
}
function FindPlugins($path){
    //echo "FindPlugins: $path \n";
    $plugins = [];
    $shared_models_dir = opendir($path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        if(is_dir($path.$file) && $file != ".." && $file != "."){
            $plugins[] = $path.$file."/";
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    return $plugins;
}
function FindPluginsLocal($path){
    //echo "FindPlugins: $path \n";
    $plugins = [];
    $shared_models_dir = opendir($path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        if(is_dir($path.$file) && $file != ".." && $file != "."){
            $plugins[] = $file."/";
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    return $plugins;
}
function FindPluginsName($path){
    //echo "FindPlugins: $path \n";
    $plugins = [];
    $shared_models_dir = opendir($path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        if(is_dir($path.$file) && $file != ".." && $file != "."){
            $plugins[] = preg_replace("/Null/","",$file);
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    return $plugins;
}
//echo "0.8\n";

function LoadJsonArray($url){
    $info = file_get_contents($url);
    return json_decode($info,true);
}

function endsWith( $haystack, $needle ) {
    $length = strlen( $needle );
    if( !$length ) {
        return true;
    }
    return substr( $haystack, -$length ) === $needle;
}

?>
<?php
if(isset($_GET['test'])) define("TEST_MODE",$_GET['test']);
define("DEBUG",true);
require_once("../includes/main.php");
$tests = [];
$tests['tests'] = CrawlTestFolder("",['all' => "http://".LocalIP()."/tests/?test=all"]);
if(isset($_GET['test'])){
    $test_name = str_replace('.php','',$_GET['test']);
    $tests['running'] = [];
    if(strpos($_GET['test'],'.php')){
        // is file
        $tests['running'][$test_name] = "Fail";
        if(is_file($root_path."tests/".$_GET['test'])) require_once($root_path."tests/".$_GET['test']);
    } else if($test_name == "all") {
        RunTestFolder("../tests/");
    } else {
        // run all test in folder
        RunTestFolder($_GET['test']);
    }
}
/**
 * go through this folder and all it's child folders and add any .php file
 * @param string $path the path of the current folder to crawl
 */
function CrawlTestFolder($path = "",$tests = []){
    if($path != ""){
        $shared_models_dir = opendir($path);
        $tests[$path] = ["$path/all"=>"http://".LocalIP()."/tests/?test=$path",'tests'=>[]];
    } else {
        $shared_models_dir = opendir("../tests/");
    }
    while ($file = readdir($shared_models_dir)) { 
        if(!is_dir($file) && strpos($file, '.php')>0 && is_file($path.$file)) { 
            if($file != "index.php"){
                $name = str_replace('.php','',$file);
                if($path == "") 
                    $tests[$name] = "http://".LocalIP()."/tests/?test=$path$file";
                else 
                    $tests[$path]['tests'][$name] = "http://".LocalIP()."/tests/?test=$path$file";        
            }
            //require_once($path.$file);
        } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
            $tests = CrawlTestFolder($path.$file."/",$tests);
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    return $tests;
}

/**
 * go through this folder and all it's child folders and add any .php file
 * @param string $path the path of the current folder to crawl
 */
function RunTestFolder($path){
    global $test_name, $tests;
    $shared_models_dir = opendir($path);
    while ($file = readdir($shared_models_dir)) { 
        if(!is_dir($file) && strpos($file, '.php')>0 && is_file($path.$file)) { 
            if($file != "index.php"){
                $test_name = str_replace('.php','',$file);
                $tests['running'][$test_name] = "Fail";
                require_once($path.$file);
            }
        } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
            RunTestFolder($path.$file."/");
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
}



if(defined("TEST_MODE") && isset($device_info) && strpos($device_info['database'],"_test") > 0)
    clsDB::$db_g->RemoveDB($device_info['database']);
OutputJson($tests);
?>
<?php
define("START_TIME",microtime(true));
//if(!defined("INCLUDE_MAIN")){
    //define("INCLUDE_MAIN",true);

    if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "0.1\n";
    if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "main 01\n";
    if(!isset($root_path)){
        $root_path = "";
        $i = 0;
        while(!is_file($root_path."includes/main.php") && $i < 10){
            $root_path .= "../"; $i++;
        }
    }

    if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "0.2\n";

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    date_default_timezone_set("America/Denver");
    if(isset($_GET['TEST_MODE'])) define("TEST_MODE",$_GET['TEST_MODE']);
    if(isset($_GET['DEBUG'])) define("DEBUG",$_GET['DEBUG']);
    if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "0.3\n";

    IncludeFolder($root_path."includes/utils/");

    IncludeFolder($root_path."models/");
    IncludeFolder($root_path."modules/");
    IncludeFolder($root_path."views/");

    // check if this exists and if not we're in setup mode
    if(is_file($root_path."settings.php")){
        require_once($root_path."settings.php");
        if(isset($db_info)){
            if(defined("TEST_MODE") && strpos($db_info['database'],"_test") === false) $db_info['database'] = $db_info['database']."_test";
            $db = new clsDB($db_info['database'], $db_info['username'], $db_info['password']);
        } else if(isset($device_info)){
            if(defined("TEST_MODE") && strpos($device_info['database'],"_test") === false) $device_info['database'] = $device_info['database']."_test";
            //Debug::Log(strpos($device_info['database'],"_test"),$device_info);
            $db = new clsDB($device_info['database'], $device_info['username'], $device_info['password']);
        } else {
            define("SETUP_MODE","broken settings.php");
        }
    } else {
        define("SETUP_MODE","missing settings.php");
    }

    if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "0.4 $root_path \n";


    if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "0.5\n";

    $plugins = FindPlugins($root_path."plugins/");

    if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "0.6\n";

    foreach($plugins as $plugin){
        if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "0.6.0 $plugin\n";
        IncludeFolder($plugin."models/");
        if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "0.6.1 $plugin\n";
        IncludeFolder($plugin."modules/"); 
        if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "0.6.2 $plugin\n";
    }
    if(defined("SETUP_MODE")){
        // we're in setup mode... now what?
        switch($_SERVER['REQUEST_URI']){
            case "/api/":
            case "/api/tasks/":
            case "/api/info/setup/":
            case "/api/info/setup/index.php":
            case "/api/info/servers/":
            case "/api/settings/":
            case "/api/user/":
            case "/api/clock/":
            case "/api/info/":
            case "/api/info/plugins/":
            case "/api/info/extensions/":
            case "/css/base.css.php":
            case "/css/plugins.css.php":
            case "/js/com/":
            case "/js/mvc/nullmvc.js.php":
                break;
            default:
                if($_SERVER['PHP_SELF'] == "/api/info/setup/index.php") break;
                $data = ['error'=>constant("SETUP_MODE")];
                $data['api'] = $_SERVER['PHP_SELF'];
                $data['server'] = $_SERVER;
                OutputJson($data);
                die();
        }
    }
    if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "0.7\n";
    /**
     * go through this folder and all it's child folders and add any .php file
     * @param string $path the path of the current folder to crawl
     */
    function IncludeFolder($path){
        if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "IncludeFolder: $path \n";
        $shared_models_dir = opendir($path);
        // LOOP OVER ALL OF THE  FILES    
        while ($file = readdir($shared_models_dir)) { 
            if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "<br><i>$file</i> ".is_dir($path.$file)."  ".is_dir($file."/")." <br>";
            // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
            if(!is_dir($file) && strpos($file, '.php')>0 && is_file($path.$file)) { 
                if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "Require: $path$file\n";
                require_once($path.$file);
                if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "included\n";
            } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
                IncludeFolder($path.$file."/");
            }
        }
        // CLOSE THE DIRECTORY
        closedir($shared_models_dir);
    }
    /**
     * a debug copy of IncludeFolder?
     * @param string $path the path to the folder that's being crawled
     */
    function IncludeFolderDebug($path){
        if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "IncludeFolder: $path \n";
        Settings::SaveSettingsVar("debug-IncludeFolder--path",$path);
        $shared_models_dir = opendir($path);
        // LOOP OVER ALL OF THE  FILES    
        while ($file = readdir($shared_models_dir)) { 
            if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "<br><i>$file</i> ".is_dir($path.$file)."  ".is_dir($file."/")." <br>";
            // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
            if(!is_dir($file) && strpos($file, '.php')>0 && is_file($path.$file)) { 
                Settings::SaveSettingsVar("debug-IncludeFolder--file",$path.$file);
                if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "Require: $path$file\n";
                require_once($path.$file);
                Settings::SaveSettingsVar("debug-IncludeFolder--included",$path.$file);
                if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "included\n";
            } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
                IncludeFolder($path.$file."/");
            }
        }
        // CLOSE THE DIRECTORY
        closedir($shared_models_dir);
    }
    /**
     * finds plugins at a given path
     * @param string $path the path to plugins...  $root_path."plugins/"
     * @return array an array of plugin relative paths "../../plugins/NullPlugin/"
     */
    function FindPlugins($path){
        if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "FindPlugins: $path \n";
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
    /**
     * finds plugins at a given path
     * @param string $path the path to plugins...  $root_path."plugins/"
     * @return array an array of plugin paths "NullPlugin/"
     */
    function FindPluginsLocal($path){
        if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "FindPlugins: $path \n";
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
    /**
     * finds the plugin names (sans the Null prefix)
     * @param string $path the path to plugins...  $root_path."plugins/"
     * @return array an array of plugins "Plugin"
     */
    function FindPluginsName($path){
        if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "FindPlugins: $path \n";
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
    if(defined("DEBUG") && constant("DEBUG") == "ECHO") echo "0.8\n";
    /**
     * loads json from url and returns a data array
     * @param string $url the url to load
     * @return array a data array of the json data
     */
    function LoadJsonArray($url){
        $info = file_get_contents($url);
        return json_decode($info,true);
    }
    /**
     * does a search to see if a string ends with another string
     * @param string $haystack the string to be searched
     * @param string $needle what needs to be at the end of the $haystack
     * @return bool returns true if the $haystack ends with $needle
     */
    function endsWith( $haystack, $needle ) {
        $length = strlen( $needle );
        if( !$length ) {
            return true;
        }
        return substr( $haystack, -$length ) === $needle;
    }
    /**
     * remap a number from the starting range to the ending range
     * @param float $start_min the low value for the starting range
     * @param float $start_max the high value for the starting range
     * @param float $end_min the low value for the ending range
     * @param float $end_max the high value for the ending range
     * @param float $value the value to be remapped from the starting range to the ending range
     */
    function map($start_min,$start_max,$end_min,$end_max,$value){
        return $end_min + ($value - $start_min) * ($end_max - $end_min) / ($start_max - $start_min);
    }
        /**
     * remap a number from the starting range to the ending range clamped to the ending range
     * @param float $start_min the low value for the starting range
     * @param float $start_max the high value for the starting range
     * @param float $end_min the low value for the ending range
     * @param float $end_max the high value for the ending range
     * @param float $value the value to be remapped from the starting range to the ending range
     */
    function mapClamped($start_min,$start_max,$end_min,$end_max,$value){
        $out = $end_min + ($value - $start_min) * ($end_max - $end_min) / ($start_max - $start_min);
        if($end_max > $end_min){
            if($out > $end_max) $out = $end_max;
            if($out < $end_min) $out = $end_min;
        } else {
            if($out < $end_max) $out = $end_max;
            if($out > $end_min) $out = $end_min;
        }
        return $out;
    }
//}
?>
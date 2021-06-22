<?php


function TestingFolderModified($path,$time = null){

    //echo "IncludeFolder: $path \n";
    $shared_models_dir = opendir($path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        //echo "<br><i>$file</i> ".is_dir($path.$file)."  ".is_dir($file."/")." <br>";
        // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
        if(!is_dir($file) && is_file($path.$file)) { 
            //echo "Require: $path$file\n";
            if(is_null($time)){
                $time = filemtime($path.$file);
            } else if(filemtime($path.$file) > $time){
                $time = filemtime($path.$file);
            }
            echo "\n".date("Y-m-d H:i:s",$time);
            //require_once($path.$file);
            //echo "included\n";
        } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
            $time = TestingFolderModified($path.$file."/",$time);
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    
    if(is_null($time)) return 0;
    return $time;
}

/**
 * the date of the latest modified file under the folder
 * @param string $path the folder to crawl
 * @return string the date of the latest modified file
 */
function FolderModifiedDate($path){
    $time = FolderModified($path);
    if($time == 0){
        return null;
    }
    return date("Y-m-d H:i:s",$time);
}
/**
 * finds the latest modified time for a folder and its sub folders
 * @param string $path the folder path to crawl. will crawl all child folders
 * @param int $count the number of files in the folder (passed to function when iterating through sub folders)
 * @return int the number of files in a folder and its sub folders
 */
function FolderFileCount($path,$count = 0){

    //echo "IncludeFolder: $path \n";
    $shared_models_dir = opendir($path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        //echo "<br><i>$file</i> ".is_dir($path.$file)."  ".is_dir($file."/")." <br>";
        // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
        if(!is_dir($file) && is_file($path.$file)) { 
            //echo "Require: $path$file\n";
            $count++;
            //require_once($path.$file);
            //echo "included\n";
        } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
            $count = FolderFileCount($path.$file."/",$count);
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    
    return $count;
}
/**
 * finds the latest modified time for a folder and it's sub folders
 * @param string $path the folder path to crawl. will crawl all child folders
 * @param string|null $time the latest modified time (passed to function when iterating through sub folders)
 * @return int the time of the latest modified file
 */
function FolderModified($path,$time = null){

    //echo "IncludeFolder: $path \n";
    $shared_models_dir = opendir($path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        //echo "<br><i>$file</i> ".is_dir($path.$file)."  ".is_dir($file."/")." <br>";
        // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
        if(!is_dir($file) && is_file($path.$file)) { 
            //echo "Require: $path$file\n";
            if(is_null($time)){
                $time = filemtime($path.$file);
            } else if(filemtime($path.$file) > $time){
                $time = filemtime($path.$file);
            }
            //require_once($path.$file);
            //echo "included\n";
        } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
            $time = FolderModified($path.$file."/",$time);
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    
    if(is_null($time)) return 0;
    return $time;
}
/**
 * crawls a folder and finds the earliest modified time and latest modified time. calls itself for all child folders
 * @param string $path the folder path to crawl. will crawl all child folders
 * @param string|null $start the earliest modified time (passed to function when iterating through sub folders)
 * @param string|null $end the latest modified time (passed to function when iterating through sub folders)
 * @param string|null $f1 the earliest modified file (passed to function when iterating through sub folders)
 * @param string|null $f2 the latest modified file (passed to function when iterating through sub folders)
 * @return array [$start,$stop,$f1,$f2]
 */
function FolderModifiedWindow($path,$start = null,$stop = null,$f1 = null, $f2 = null){

    //echo "IncludeFolder: $path \n";
    $shared_models_dir = opendir($path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        //echo "<br><i>$file</i> ".is_dir($path.$file)."  ".is_dir($file."/")." <br>";
        // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
        if(!is_dir($file) && is_file($path.$file)) { 
            //echo "Require: $path$file\n";
            if(is_null($start)){
                $start = filemtime($path.$file);
                $f1 = $path.$file;
            } else if(filemtime($path.$file) < $start){
                $start = filemtime($path.$file);
                $f1 = $path.$file;
            }
            if(is_null($stop)){
                $stop = filemtime($path.$file);
                $f2 = $path.$file;
            } else if(filemtime($path.$file) > $stop){
                $stop = filemtime($path.$file);
                $f2 = $path.$file;
            }
            //require_once($path.$file);
            //echo "included\n";
        } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
            list($start,$stop,$f1,$f2) = FolderModifiedWindow($path.$file."/",$start,$stop,$f1,$f2);
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    
    return [$start,$stop,$f1,$f2];
}
/**
 * crawls a folder and finds the earliest changed time and latest change time. calls itself for all child folders
 * @param string $path the folder path to crawl. will crawl all child folders
 * @param string|null $start the earliest changed time (passed to function when iterating through sub folders)
 * @param string|null $end the latest changed time (passed to function when iterating through sub folders)
 * @param string|null $f1 the earliest changed file (passed to function when iterating through sub folders)
 * @param string|null $f2 the latest changed file (passed to function when iterating through sub folders)
 * @return array [$start,$stop,$f1,$f2]
 */
function FolderChangedWindow($path,$start = null,$stop = null,$f1 = null,$f2 = null){

    //echo "IncludeFolder: $path \n";
    $shared_models_dir = opendir($path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        //echo "<br><i>$file</i> ".is_dir($path.$file)."  ".is_dir($file."/")." <br>";
        // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
        if(!is_dir($file) && is_file($path.$file)) { 
            //echo "Require: $path$file\n";
            if(is_null($start)){
                $start = filectime($path.$file);
                $f1 = $path.$file;
            } else if(filectime($path.$file) < $start){
                $start = filectime($path.$file);
                $f1 = $path.$file;
            }
            if(is_null($stop)){
                $stop = filectime($path.$file);
                $f2 = $path.$file;
            } else if(filectime($path.$file) > $stop){
                $stop = filectime($path.$file);
                $f2 = $path.$file;
            }
            //require_once($path.$file);
            //echo "included\n";
        } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
            list($start,$stop,$f1,$f2) = FolderChangedWindow($path.$file."/",$start,$stop,$f1,$f2);
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    
    return [$start,$stop,$f1,$f2];
}
/**
 * generates a hash from the modified ranges for the includes, api, models, and modules folders if they exist. 
 * @param string $path the root path of the plugin/extension/main project
 * @return string a crc32b hash of the folder modified ranges
 */
function FolderHash($path){
    $hash = "";
    if(is_dir($path."includes/")){
        $includes = FolderModifiedWindow($path."includes/");
        $hash .= $includes[0].$includes[1];
    }
    if(is_dir($path."api/")){
        $api = FolderModifiedWindow($path."api/");
        $hash .= $api[0].$api[1];
    }
    if(is_dir($path."models/")){
        $models = FolderModifiedWindow($path."models/");
        $hash .= $models[0].$models[1];
    }
    if(is_dir($path."modules/")){
        $modules = FolderModifiedWindow($path."modules/");
        $hash .= $modules[0].$modules[1];
    }
    if($hash == ""){
        return "hash failed";
    }
    return hash("crc32b",$hash);
    //return hash("crc32b",FolderModifiedDate($path."api/").FolderModifiedDate($path."models/").FolderModifiedDate($path."modules/"));
}
/**
 * generates a hash from the modified ranges for the includes, api, models, and modules folders if they exist. 
 * @param string $path the root path of the plugin/extension/main project
 * @return string a crc32b hash of the folder modified ranges
 */
function FolderHashDate($path){
    $time = 0;
    if(is_dir($path."includes/")){
        $t = FolderModified($path."includes/");
        if($time < $t) $time = $t;
    }
    if(is_dir($path."api/")){
        $t = FolderModified($path."api/");
        if($time < $t) $time = $t;
    }
    if(is_dir($path."models/")){
        $t = FolderModified($path."models/");
        if($time < $t) $time = $t;
    }
    if(is_dir($path."modules/")){
        $t = FolderModified($path."modules/");
        if($time < $t) $time = $t;
    }
    return date("Y-m-d H:i:s",$time);
    //return hash("crc32b",FolderModifiedDate($path."api/").FolderModifiedDate($path."models/").FolderModifiedDate($path."modules/"));
}

?>
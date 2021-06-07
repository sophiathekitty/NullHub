<?php
function FolderModifiedDate($path){
    $time = FolderModified($path);
    if($time == 0){
        return null;
    }
    return date("Y-m-d H:i:s",$time);
}

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
?>
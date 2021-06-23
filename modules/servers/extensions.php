<?php
function LocalExtensions($verbose = false){
    global $root_path;
    return ExtensionsFolder($root_path,"extensions/",$verbose);
}



function FindLocalExtensions(){
    global $root_path;
    $shared_models_dir = opendir($root_path."extensions/");
    // LOOP OVER ALL OF THE  FILES
    $extensions = [];
    while ($file = readdir($shared_models_dir)) { 
        if(is_dir($root_path."extensions/".$file) && $file != ".." && $file != "."){
            $extensions[] = $file;
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    return $extensions;    
}


function ExtensionsFolder($root,$path, $verbose = false){
    $extensions = [];
    //echo "$root$path\n";
    $shared_models_dir = opendir($root.$path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        //echo "$file\n";
        // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
        if(is_dir($root.$path.$file) && is_dir($root.$path.$file."/api") && $file != ".." && $file != "."){
            //echo "http://".LocalIp().Settings::LoadSettingsVar('path',"/").$path.$file."/api/info";
            //$info = file_get_contents("http://".LocalIp().Settings::LoadSettingsVar('path',"/").$path.$file."/api/info");
            //$data = json_decode($info,true);
            //$extensions[$file] = $data['info'];
            $extension = ['id'=>$file];
            if(is_file("$root$path$file/site.webmanifest")){
                $info = file_get_contents("$root$path$file/site.webmanifest");
                $data = json_decode($info);
                $extension['name'] = $data->name;
                if(isset($data->git)) $extension['git'] = $data->git;
            } else if(is_file("$root$path$file/manifest.json")){
                $info = file_get_contents("$root$path$file/manifest.json");
                $data = json_decode($info);
                $extension['name'] = $data->name;
                if(isset($data->git)) $extension['git'] = $data->git;
            } else {
                $extension['error'] = "manifest missing";
            }
            
            $extension['path'] = "http://".LocalIp().Settings::LoadSettingsVar('path',"/").$path.$file."/";
            $extension['app'] = "http://".LocalIp().Settings::LoadSettingsVar('path',"/").$path.$file."/app";
            $extension['api'] = "http://".LocalIp().Settings::LoadSettingsVar('path',"/").$path.$file."/api";
            $extension['hash'] = FolderHash($root.$path.$file."/"); //hash("crc32b",FolderModifiedDate($root.$path.$file."/api/").FolderModifiedDate($root.$path.$file."/models/").FolderModifiedDate($root.$path.$file."/modules/"));
            $extension['modified'] = date("Y-m-d H:i:s",FolderModified($root.$path.$file."/"));
            if($verbose){
                $extension['files'] = FolderFileCount($root.$path.$file."/");
                $extension['folders'] = [];
                $extension['folders']['api'] = FolderModifiedDate($root.$path.$file."/api/");
                $extension['folders']['app'] = FolderModifiedDate($root.$path.$file."/app/");
                $extension['folders']['includes'] = FolderModifiedDate($root.$path.$file."/includes/");
                $extension['folders']['models'] = FolderModifiedDate($root.$path.$file."/models/");
                $extension['folders']['modules'] = FolderModifiedDate($root.$path.$file."/modules/");
                $extension['folders']['python'] = FolderModifiedDate($root.$path.$file."/python/");
                $extension['folders']['templates'] = FolderModifiedDate($root.$path.$file."/templates/");    
            }
                $extensions[] = $extension;
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    return $extensions;
}
?>
<?php
function LocalPluginApis($apis){
    global $root_path;
    $plugins = FindPluginsLocal($root_path."plugins/");
    foreach($plugins as $plugin){
        $apis = APIChildFolder($root_path,"plugins/".$plugin."api/",substr($plugin,0,(strlen($plugin)-1)),$apis);
    }
    return $apis;
}
function LocalPluginInfo($verbose = false){
    global $root_path;
    $plugins_local = FindPluginsLocal($root_path."plugins/");
    $plugins = [];
    foreach($plugins_local as $plugin_path){
        $name = substr($plugin_path,strpos($plugin_path,"plugins/"));
        $name = substr($name,0,strlen($name)-1);
        $plugin = LoadJsonArray($root_path."plugins/".$plugin_path."version.json");
        $plugin['local'] = "http://".LocalIP()."/plugins/".$plugin_path;
        $plugin['api'] = "http://".LocalIP()."/plugins/".$plugin_path."api/";
        $plugin['id'] = $name;
        $plugin['modified'] = FolderModifiedDate($root_path."plugins/".$plugin_path);
        if($verbose){
            $plugin['files'] = FolderFileCount($root_path."plugins/".$plugin_path);
            $plugin['api_modified'] = FolderModifiedDate($root_path."plugins/".$plugin_path."api/");
            $plugin['models_modified'] = FolderModifiedDate($root_path."plugins/".$plugin_path."models/");
            $plugin['modules_modified'] = FolderModifiedDate($root_path."plugins/".$plugin_path."modules/");
            $plugin['python_modified'] = FolderModifiedDate($root_path."plugins/".$plugin_path."python/");
            $plugin['templates_modified'] = FolderModifiedDate($root_path."plugins/".$plugin_path."templates/");    
        }
        $plugin['hash'] = FolderHash($root_path."plugins/".$plugin_path);//hash("crc32b",FolderModifiedDate($root_path."plugins/".$plugin_path."api/").FolderModifiedDate($root_path."plugins/".$plugin_path."models/").FolderModifiedDate($root_path."plugins/".$plugin_path."modules/"));
        $plugins[] = $plugin;
    }
    return $plugins;
}


?>
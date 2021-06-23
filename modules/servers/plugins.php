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
            $plugin['folders'] = [];
            $plugin['folders']['api'] = FolderModifiedDate($root_path."plugins/".$plugin_path."api/");
            $plugin['folders']['models'] = FolderModifiedDate($root_path."plugins/".$plugin_path."models/");
            $plugin['folders']['modules'] = FolderModifiedDate($root_path."plugins/".$plugin_path."modules/");
            $plugin['folders']['python'] = FolderModifiedDate($root_path."plugins/".$plugin_path."python/");
            $plugin['folders']['templates'] = FolderModifiedDate($root_path."plugins/".$plugin_path."templates/");    
        }
        $plugin['hash'] = FolderHash($root_path."plugins/".$plugin_path);//hash("crc32b",FolderModifiedDate($root_path."plugins/".$plugin_path."api/").FolderModifiedDate($root_path."plugins/".$plugin_path."models/").FolderModifiedDate($root_path."plugins/".$plugin_path."modules/"));
        $plugins[] = $plugin;
    }
    return $plugins;
}


?>
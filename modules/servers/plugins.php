<?php
/**
 * check to see if the plugin folder exists
 */
function HasPlugin($plugin){
    global $root_path;
    return is_dir($root_path."plugins/".$plugin);
}
function HasPluginBoolAsString($plugin){
    if(HasPlugin($plugin)) return "true";
    return "false";
}
/**
 * gets the apis for a plugin
 * @param string $plugin the plugin name ie: "NullWeather"
 * @return array array of plugin apis
 */
function PluginAPIs($plugin){
    global $root_path;
    $apis = [];
    return APIChildFolder($root_path,"plugins/".$plugin."api/",substr($plugin,0,(strlen($plugin)-1)),$apis);
}
/**
 * gets the apis for local plugins
 * @param array $apis array of apis
 * @return array array of apis including plugin apis
 */

function LocalPluginApis($apis){
    global $root_path;
    $plugins = FindPluginsLocal($root_path."plugins/");
    foreach($plugins as $plugin){
        $apis = APIChildFolder($root_path,"plugins/".$plugin."api/",substr($plugin,0,(strlen($plugin)-1)),$apis);
    }
    return $apis;
}
/**
 * gets local plugin info
 * @param bool $verbose return verbose plugin info
 * @return array array of plugin info
 */
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
        $plugin['hash'] = GitHash($root_path."plugins/".$plugin_path);//FolderHash($root_path."plugins/".$plugin_path);//hash("crc32b",FolderModifiedDate($root_path."plugins/".$plugin_path."api/").FolderModifiedDate($root_path."plugins/".$plugin_path."models/").FolderModifiedDate($root_path."plugins/".$plugin_path."modules/"));
        $plugins[] = $plugin;
    }
    return $plugins;
}


?>
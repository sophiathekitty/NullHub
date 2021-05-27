<?php
function LocalPluginApis($apis){
    global $root_path;
    $plugins = FindPluginsLocal($root_path."plugins/");
    foreach($plugins as $plugin){
        $apis = APIChildFolder($root_path,"plugins/".$plugin."api/",substr($plugin,0,(strlen($plugin)-1)),$apis);
    }
    return $apis;
}
?>
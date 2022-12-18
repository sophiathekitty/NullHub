<?php
require_once("../includes/main.php");
Services::Start("NullHub::Setup");
Services::Log("NullHub::Setup","SyncServers");
SyncServers();
Services::Log("NullHub::Setup","SyncRooms");
SyncRooms();
Services::Log("NullHub::Setup","UserSync::pull");
UserSync::pull();
Services::Log("NullHub::Setup","SyncColorPallets");
SyncColorPallets();
// find plugin services
$plugins = FindPlugins($root_path."plugins/");
define('main_already_included',true);
Services::Log("NullHub::Setup","Plugins -- start");
foreach($plugins as $plugin){
    if(is_file($plugin."services/setup.php")){
        Services::Log("NullHub::Setup","Plugins -- $plugin");
        require_once($plugin."services/setup.php");
    }
}
Services::Log("NullHub::Setup","Plugins -- done");
// extensions 
$extensions = LocalExtensions();
Services::Log("NullHub::Setup","Extensions -- start");
foreach($extensions as $extension){
    if(is_file($root_path.$extension['local']."services/setup.php")){
        Services::Log("NullHub::Setup","Extensions -- ".$extension['id']);
        $info = file_get_contents($extension['path']."services/setup.php");
    }
}
Services::Log("NullHub::Setup","Extensions -- done");
Services::Complete("NullHub::Setup");
OutputJson([]);
?>
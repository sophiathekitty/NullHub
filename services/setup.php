<?php
require_once("../includes/main.php");
SyncServers();
SyncRooms();
UserSync::pull();
// find plugin services
$plugins = FindPlugins($root_path."plugins/");
define('main_already_included',true);
foreach($plugins as $plugin){
    if(is_file($plugin."services/setup.php")){
        require_once($plugin."services/setup.php");
    }
}
// extensions 
$extensions = LocalExtensions();
foreach($extensions as $extension){
    if(is_file($extension['path']."services/setup.php"))
        $info = file_get_contents($extension['path']."services/setup.php");
}
OutputJson([]);
?>
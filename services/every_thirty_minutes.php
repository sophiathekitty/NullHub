<?php
require_once("../includes/main.php");
Services::Start("NullHub::EveryThirtyMinutes");
// find plugin services
$plugins = FindPlugins($root_path."plugins/");
define('main_already_included',true);
Services::Log("NullHub::EveryThirtyMinutes","Plugins -- start");
foreach($plugins as $plugin){
    if(is_file($plugin."services/every_thirty_minutes.php")){
        try{
            Services::Log("NullHub::EveryThirtyMinutes","Plugins -- $plugin");
            require_once($plugin."services/every_thirty_minutes.php");
        } catch(Error $e){
            Services::Error("NullHub::EveryThirtyMinutes",$e->getMessage());
        }
    }
}
Services::Log("NullHub::EveryThirtyMinutes","Plugins -- done");
// extensions 
$extensions = LocalExtensions();
Services::Log("NullHub::EveryThirtyMinutes","Extensions -- start");
foreach($extensions as $extension){
    if(is_file($root_path.$extension['local']."services/every_thirty_minutes.php")){
        try{
            Services::Log("NullHub::EveryThirtyMinutes","Extensions -- ".$extension['id']);
            $info = file_get_contents($extension['path']."services/every_thirty_minutes.php");
        } catch(Error $e){
            Services::Error("NullHub::EveryThirtyMinutes",$e->getMessage());
        }
    }
}
Services::Log("NullHub::EveryThirtyMinutes","Extensions -- done");
// complete
Services::Complete("NullHub::EveryThirtyMinutes");
OutputJson([]);
?>
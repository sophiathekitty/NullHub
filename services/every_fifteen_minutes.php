<?php
require_once("../includes/main.php");
Services::Start("NullHub::EveryFifteenMinutes");
// find plugin services
$plugins = FindPlugins($root_path."plugins/");
define('main_already_included',true);
Services::Log("NullHub::EveryFifteenMinutes","Plugins -- start");
foreach($plugins as $plugin){
    if(is_file($plugin."services/every_fifteen_minutes.php")){
        try{
            Services::Log("NullHub::EveryFifteenMinutes","Plugins -- $plugin");
            require_once($plugin."services/every_fifteen_minutes.php");
        } catch(Error $e){
            Services::Error("NullHub::EveryFifteenMinutes",$e->getMessage());
        }
    }
}
Services::Log("NullHub::EveryFifteenMinutes","Plugins -- done");
// extensions 
$extensions = LocalExtensions();
Services::Log("NullHub::EveryFifteenMinutes","Extensions -- start");
foreach($extensions as $extension){
    if(is_file($root_path.$extension['local']."services/every_fifteen_minutes.php")){
        try{
            Services::Log("NullHub::EveryFifteenMinutes","Extensions -- ".$extension['id']);
            $info = file_get_contents($extension['path']."services/every_fifteen_minutes.php");
        } catch(Error $e){
            Services::Error("NullHub::EveryFifteenMinutes",$e->getMessage());
        }
    }
}
Services::Log("NullHub::EveryFifteenMinutes","Extensions -- done");
// complete
Services::Complete("NullHub::EveryFifteenMinutes");
OutputJson([]);
?>
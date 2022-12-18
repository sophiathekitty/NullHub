<?php
require_once("../includes/main.php");
//Settings::SaveSettingsVar("Services::EveryMinuteStart",date("H:i:s"));
Services::Start("NullHub::EveryMinute");

//CheckOfflineHub(); // check if the main hub is offline or not
// pull remote tasks once a minute if this isn't the hub
Services::Log("NullHub::EveryMinute","TaskManager::AutomateTasks");
TaskManager::AutomateTasks();
Services::Log("NullHub::EveryMinute","SyncRooms");
SyncRoom();
// find plugin services
$plugins = FindPlugins($root_path."plugins/");
define('main_already_included',true);
Services::Log("NullHub::EveryMinute","Plugins -- start");
foreach($plugins as $plugin){
    if(is_file($plugin."services/every_minute.php")){
        Services::Log("NullHub::EveryMinute","Plugins -- $plugin");
        require_once($plugin."services/every_minute.php");
    }
}
Services::Log("NullHub::EveryMinute","Plugins -- done");
// extensions 
$extensions = LocalExtensions();
Services::Log("NullHub::EveryMinute","Extensions -- start ".count($extensions));
foreach($extensions as $extension){
    if(is_file($root_path.$extension['local']."services/every_minute.php")){
        Services::Log("NullHub::EveryMinute","Extensions -- ".$extension['id']." ".$extension['path']."services/every_minute.php");
        $info = file_get_contents($extension['path']."services/every_minute.php");
    } else {
        Services::Log("NullHub::EveryMinute","Extensions -missing?- ".$root_path.$extension['local']."services/every_minute.php");
    }
}


Services::Log("NullHub::EveryMinute","Extensions -- done");
Services::Complete("NullHub::EveryMinute");
//Settings::SaveSettingsVar("Services::EveryMinuteDone",date("H:i:s"));
OutputJson([]);
?>
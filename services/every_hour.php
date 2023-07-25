<?php
require_once("../includes/main.php");
//Settings::SaveSettingsVar("Services::EveryHourStart",date("H:i:s"));
Services::Start("NullHub::EveryHour");
Services::Log("NullHub::EveryHour","SyncRooms");
try{
    SyncRooms();
} catch(Error $e){
    Services::Error("NullHub::EveryHour",$e->getMessage());
}
Services::Log("NullHub::EveryHour","SyncNeighbors");
try{
    SyncNeighbors();
} catch(Error $e){
    Services::Error("NullHub::EveryHour",$e->getMessage());
}
Services::Log("NullHub::EveryHour","SyncServers");
try{
    SyncServers();
} catch(Error $e){
    Services::Error("NullHub::EveryHour",$e->getMessage());
}
Services::Log("NullHub::EveryHour","nMapCrawler::FindHosts");
try{
    nMapCrawler::FindHosts();
} catch(Error $e){
    Services::Error("NullHub::EveryHour",$e->getMessage());
}
Services::Log("NullHub::EveryHour","Elections::RunElection");
try{
    Elections::RunElection();
} catch(Error $e){
    Services::Error("NullHub::EveryHour",$e->getMessage());
}
// find plugin services
$plugins = FindPlugins($root_path."plugins/");
define('main_already_included',true);
Services::Log("NullHub::EveryHour","Plugins -- start");
foreach($plugins as $plugin){
    if(is_file($plugin."services/every_hour.php")){
        try{
            Services::Log("NullHub::EveryHour","Plugins -- $plugin");
            require_once($plugin."services/every_hour.php");
        } catch(Error $e){
            Services::Error("NullHub::EveryHour",$e->getMessage());
        }
    }
}
Services::Log("NullHub::EveryHour","Plugins -- done");
// extensions 
$extensions = LocalExtensions();
Services::Log("NullHub::EveryHour","Extensions -- start");
foreach($extensions as $extension){
    if(is_file($root_path.$extension['local']."services/every_hour.php")){
        try{
            Services::Log("NullHub::EveryHour","Extensions -- ".$extension['id']);
            $info = file_get_contents($extension['path']."services/every_hour.php");
        } catch(Error $e){
            Services::Error("NullHub::EveryHour",$e->getMessage());
        }
    }
}
Services::Log("NullHub::EveryHour","Extensions -- done");

Services::Complete("NullHub::EveryHour");
//Settings::SaveSettingsVar("Services::EveryHourDone",date("H:i:s"));
OutputJson([]);
?>
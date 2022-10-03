<?php
require_once("../includes/main.php");
//Settings::SaveSettingsVar("Services::EveryDayStart",date("m-d"));
Services::Start("NullHub::EveryDay");
Services::Log("NullHub::EveryDay","nMap::ForgetUnknown");
nMap::ForgetUnknown();
Services::Log("NullHub::EveryDay","UserSync::pull");
UserSync::pull();
Services::Log("NullHub::EveryDay","Elections::CompleteElection");
Elections::CompleteElection();    
// find plugin services
$plugins = FindPlugins($root_path."plugins/");
define('main_already_included',true);
Services::Log("NullHub::EveryDay","Plugins -- start");
foreach($plugins as $plugin){
    if(is_file($plugin."services/every_day.php")){
        Services::Log("NullHub::EveryDay","Plugins -- $plugin");
        require_once($plugin."services/every_day.php");
    }
}
Services::Log("NullHub::EveryDay","Plugins -- done");
// extensions 
$extensions = LocalExtensions();
Services::Log("NullHub::EveryDay","Extensions -- start");
foreach($extensions as $extension){
    if(is_file($extension['path']."services/every_day.php")){
        Services::Log("NullHub::EveryDay","Extensions -- ".$extension['id']);
        $info = file_get_contents($extension['path']."services/every_day.php");
    }
}
Services::Log("NullHub::EveryDay","Extensions -- done");

Services::Complete("NullHub::EveryDay");
//Settings::SaveSettingsVar("Services::EveryDayDone",date("m-d"));
OutputJson([]);
?>
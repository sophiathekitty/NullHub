<?php
require_once("../includes/main.php");
//Settings::SaveSettingsVar("Services::EveryWeekStart",date("m-d"));
Services::Start("NullHub::EveryWeek");
Services::Log("NullHub::EveryWeek","nMap::ForgetAll");
nMap::ForgetAll();
// find plugin services
$plugins = FindPlugins($root_path."plugins/");
define('main_already_included',true);
Services::Log("NullHub::EveryWeek","Plugins -- start");
foreach($plugins as $plugin){
    if(is_file($plugin."services/every_week.php")){
        Services::Log("NullHub::EveryWeek","Plugins -- $plugin");
        require_once($plugin."services/every_week.php");
    }
}
Services::Log("NullHub::EveryWeek","nMap::Plugins -- done");
// extensions 
$extensions = LocalExtensions();
Services::Log("NullHub::EveryWeek","Extensions -- start");
foreach($extensions as $extension){
    if(is_file($extension['path']."services/every_week.php")){
        Services::Log("NullHub::EveryWeek","Extensions -- ".$extension['id']);
        $info = file_get_contents($extension['path']."services/every_week.php");
    }
}
Services::Log("NullHub::EveryWeek","Extensions -- done");

//Settings::SaveSettingsVar("Services::EveryWeekDone",date("m-d"));
Services::Complete("NullHub::EveryWeek");
OutputJson([]);
?>
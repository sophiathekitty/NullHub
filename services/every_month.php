<?php
require_once("../includes/main.php");
Services::Start("NullHub::EveryMonth");
//Settings::SaveSettingsVar("Services::EveryMonthStart",date("m-d"));
// find plugin services
$plugins = FindPlugins($root_path."plugins/");
define('main_already_included',true);
Services::Log("NullHub::EveryMonth","Plugins -- start");
foreach($plugins as $plugin){
    if(is_file($plugin."services/every_month.php")){
        Services::Log("NullHub::EveryMonth","Plugins -- $plugin");
        require_once($plugin."services/every_month.php");
    }
}
Services::Log("NullHub::EveryMonth","Plugins -- done");
// extensions 
$extensions = LocalExtensions();
Services::Log("NullHub::EveryMonth","Extensions -- start");
foreach($extensions as $extension){
    if(is_file($root_path.$extension['local']."services/every_month.php")){
        Services::Log("NullHub::EveryMonth","Extensions -- ".$extension['id']);
        $info = file_get_contents($extension['path']."services/every_month.php");
    }
}
Services::Log("NullHub::EveryMonth","Extensions -- done");

Services::Complete("NullHub::EveryMonth");
//Settings::SaveSettingsVar("Services::EveryMonthDone",date("m-d"));
OutputJson([]);
?>
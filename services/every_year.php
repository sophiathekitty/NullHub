<?php
require_once("../includes/main.php");
Services::Start("NullHub::EveryYear");
//Settings::SaveSettingsVar("Services::EveryYearStart",date("Y"));
// find plugin services
$plugins = FindPlugins($root_path."plugins/");
define('main_already_included',true);
Services::Log("NullHub::EveryYear","Plugins -- start");
foreach($plugins as $plugin){
    if(is_file($plugin."services/every_year.php")){
        Services::Log("NullHub::EveryYear","Plugins -- $plugin");
        require_once($plugin."services/every_year.php");
    }
}
Services::Log("NullHub::EveryYear","Plugins -- done");
// extensions 
$extensions = LocalExtensions();
Services::Log("NullHub::EveryYear","Extensions -- start");
foreach($extensions as $extension){
    if(is_file($root_path.$extension['local']."services/every_year.php")){
        Services::Log("NullHub::EveryYear","Extensions -- ".$extension['id']);
        $info = file_get_contents($extension['path']."services/every_year.php");
    }
}
Services::Log("NullHub::EveryYear","Extensions -- done");

Services::Complete("NullHub::EveryYear");
//Settings::SaveSettingsVar("Services::EveryYearDone",date("Y"));
OutputJson([]);
?>
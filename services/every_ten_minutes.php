<?php
$min = substr(date("i"),1);
//echo "[$min]\n";
if($min == "1" || (int)$min == 1){
    if(!defined("EVERY_MINUTE")) require_once("../includes/main.php");
    Services::Start("NullHub::EveryTenMinutes");
    //Settings::SaveSettingsVar("Services::EveryTenMinutesStart",date("H:i:s"));
    try{
        Services::Log("NullHub::EveryTenMinutes","CheckOfflineHub");
        CheckOfflineHub(); // check if the main hub is offline or not
    } catch(Error $e){
        Services::Error("NullHub::EveryTenMinutes",$e->getMessage());
    }
// find plugin services
    $plugins = FindPlugins($root_path."plugins/");
    define('main_already_included',true);
    Services::Log("NullHub::EveryTenMinutes","Plugins -- start");
    foreach($plugins as $plugin){
        if(is_file($plugin."services/every_ten_minutes.php")){
            try{
                Services::Log("NullHub::EveryTenMinutes","Plugins -- $plugin");
                require_once($plugin."services/every_ten_minutes.php");
            } catch(Error $e){
                Services::Error("NullHub::EveryTenMinutes",$e->getMessage());
            }
            //Services::Log("NullHub::EveryTenMinutes","Plugins -- $plugin -- done");
        }
    }
    Services::Log("NullHub::EveryTenMinutes","Plugins -- done");
    // extensions 
    $extensions = LocalExtensions();
    Services::Log("NullHub::EveryTenMinutes","Extensions -- start");
    foreach($extensions as $extension){
        if(is_file($root_path.$extension['local']."services/every_ten_minutes.php")){
            try{
                Services::Log("NullHub::EveryTenMinutes","Extensions -- ".$extension['id']);
                $info = file_get_contents($extension['path']."services/every_ten_minutes.php");
            } catch(Error $e){
                Services::Error("NullHub::EveryTenMinutes",$e->getMessage());
            }
        }
    }
    Services::Log("NullHub::EveryTenMinutes","Extensions -- done");
    Services::Complete("NullHub::EveryTenMinutes");
    //Settings::SaveSettingsVar("Services::EveryTenMinutesDone",date("H:i:s"));
}
if(!defined("EVERY_MINUTE")) OutputJson([]);
?>
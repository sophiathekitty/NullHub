<?php
$min = substr(date("i"),1);
//echo "minute: $min == 0 or 5?\n";
if($min == "0" || $min == "5" || (int)$min == 0 || (int)$min == 5){
    require_once("../includes/main.php");
    //Settings::SaveSettingsVar("Services::EveryFiveMinutesStart",date("H:i:s"));
    Services::Start("NullHub::EveryFiveMinutes");
    Services::Log("NullHub::EveryFiveMinutes","nMapCrawler::CheckHosts");
    nMapCrawler::CheckHosts();
    // find plugin services
    $plugins = FindPlugins($root_path."plugins/");
    define('main_already_included',true);
    Services::Log("NullHub::EveryFiveMinutes","Plugins -- start");
    foreach($plugins as $plugin){
        //echo "plugin: $plugin\n";
        if(is_file($plugin."services/every_five_minutes.php")){
            //echo "plugin: $plugin has ever five minutes\n";
            Services::Log("NullHub::EveryFiveMinutes","Plugins -- $plugin");
            require_once($plugin."services/every_five_minutes.php");
        }
    }
    Services::Log("NullHub::EveryFiveMinutes","Plugins -- done");

    // extensions 
    $extensions = LocalExtensions();
    Services::Log("NullHub::EveryFiveMinutes","Extensions -- start");
    foreach($extensions as $extension){
        if(is_file($extension['path']."services/every_five_minutes.php")){
            Services::Log("NullHub::EveryFiveMinutes","Extensions -- ".$extension['id']);
            $info = file_get_contents($extension['path']."services/every_five_minutes.php");
        }
    }
    Services::Log("NullHub::EveryFiveMinutes","Extensions -- done");

    Services::Complete("NullHub::EveryFiveMinutes");
    //Settings::SaveSettingsVar("Services::EveryFiveMinutesDone",date("H:i:s"));
}
OutputJson([]);
?>
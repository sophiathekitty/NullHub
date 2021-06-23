<pre><?php
$min = substr(date("i"),1);
echo "minute: $min == 0 or 5?\n";
if($min == "0" || $min == "5" || (int)$min == 0 || (int)$min == 5){
    require_once("../includes/main.php");
    Settings::SaveSettingsVar("Services::EveryFiveMinutesStart",date("H:i:s"));

    nMapCrawler::CheckHosts();
    // find plugin services
    $plugins = FindPlugins($root_path."plugins/");
    define('main_already_included',true);
    foreach($plugins as $plugin){
        echo "plugin: $plugin\n";
        if(is_file($plugin."services/every_five_minutes.php")){
            echo "plugin: $plugin has ever five minutes\n";
            require_once($plugin."services/every_five_minutes.php");
        }
    }
    Settings::SaveSettingsVar("Services::EveryFiveMinutesDone",date("H:i:s"));
}

?></pre>
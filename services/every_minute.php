<pre><?php
require_once("../includes/main.php");
Settings::SaveSettingsVar("Services::EveryMinuteStart",date("H:i:s"));
//CheckOfflineHub(); // check if the main hub is offline or not
// pull remote tasks once a minute if this isn't the hub
RemoteTasks::PullRemoteTasks();
SyncRoom();
// find plugin services
$plugins = FindPlugins($root_path."plugins/");
define('main_already_included',true);
foreach($plugins as $plugin){
    if(is_file($plugin."services/every_minute.php")){
        require_once($plugin."services/every_minute.php");
    }
}
// extensions 
$extensions = LocalExtensions();
foreach($extensions as $extension){
    $info = file_get_contents($extension['path']."services/every_minute.php");
}
Settings::SaveSettingsVar("Services::EveryMinuteDone",date("H:i:s"));
?></pre>
<pre><?php
require_once("../includes/main.php");
Settings::SaveSettingsVar("Services::EveryDayStart",date("m-d"));
nMap::ForgetUnknown();
UserSync::pull();
// find plugin services
$plugins = FindPlugins($root_path."plugins/");
define('main_already_included',true);
foreach($plugins as $plugin){
    if(is_file($plugin."services/every_day.php")){
        require_once($plugin."services/every_day.php");
    }
}
Settings::SaveSettingsVar("Services::EveryDayDone",date("m-d"));
?></pre>
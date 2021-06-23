<pre><?php
require_once("../includes/main.php");
Settings::SaveSettingsVar("Services::EveryWeekStart",date("m-d"));
// find plugin services
$plugins = FindPlugins($root_path."plugins/");
define('main_already_included',true);
foreach($plugins as $plugin){
    if(is_file($plugin."services/every_week.php")){
        require_once($plugin."services/every_week.php");
    }
}
Settings::SaveSettingsVar("Services::EveryWeekDone",date("m-d"));
?></pre>
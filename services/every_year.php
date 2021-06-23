<pre><?php
require_once("../includes/main.php");
Settings::SaveSettingsVar("Services::EveryYearStart",date("Y"));
// find plugin services
$plugins = FindPlugins($root_path."plugins/");
define('main_already_included',true);
foreach($plugins as $plugin){
    if(is_file($plugin."services/every_year.php")){
        require_once($plugin."services/every_year.php");
    }
}
Settings::SaveSettingsVar("Services::EveryYearDone",date("Y"));
?></pre>
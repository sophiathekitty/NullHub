<pre><?php
require_once("../includes/main.php");
SyncRooms();
// find plugin services
$plugins = FindPlugins($root_path."plugins/");
define('main_already_included',true);
foreach($plugins as $plugin){
    if(is_file($plugin."services/every_hour.php")){
        require_once($plugin."services/every_hour.php");
    }
}
?></pre>
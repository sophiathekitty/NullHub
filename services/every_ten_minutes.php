<pre><?php
$min = substr(date("i"),1);
echo "[$min]\n";
if($min == "1" || (int)$min == 1){
    require_once("../includes/main.php");
    CheckOfflineHub(); // check if the main hub is offline or not
    // find plugin services
    $plugins = FindPlugins($root_path."plugins/");
    define('main_already_included',true);
    foreach($plugins as $plugin){
        if(is_file($plugin."services/every_ten_minute.php")){
            require_once($plugin."services/every_teb_minute.php");
        }
    }
}
?></pre>
<pre><?php
$min = substr(date("i"),1);
echo "[$min]\n";
if($min == "0" || $min == "5" || (int)$min == 0 || (int)$min == 5){
    require_once("../includes/main.php");
    nMapCrawler::CheckHosts();
    // find plugin services
    $plugins = FindPlugins($root_path."plugins/");
    define('main_already_included',true);
    foreach($plugins as $plugin){
        if(is_file($plugin."services/every_five_minute.php")){
            require_once($plugin."services/every_five_minute.php");
        }
    }
}
?></pre>
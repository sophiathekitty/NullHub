<pre><?php
require_once("../includes/main.php");
//$servers = new Servers();
//$servers->ValidateTable();
//SyncRooms();
//RemoteTasks::PullRemoteTasks();
//print_r(Tasks::LoadDueTasks());
//echo clsDB::$db_g->get_err();
//echo clsDB::$db_g->last_sql;
/*
echo LocalIp()."\n";
echo LocalMac()."\n";
//print_r(nMapHosts());
//nMapCrawler::CheckHosts();
print_r(Servers::GetHub());
if(Servers::IsHub()) echo "this is the hub\n";
nMapCrawler::CheckHosts();
*/
//echo "\n\n".date("Y-m-d H:i:s",TestingFolderModified("../plugins/NullWeather/"));
//echo "\n\n".FolderFileCount("../plugins/NullWeather/");
//echo PullRemoteSensors::Sync();
//SyncServers();
/*
if($_GET['ip']){
    $data = GoogleIPStatus($_GET['ip']);
    print_r($data);
    echo "\n\n".$_GET['ip'];
} else {
    $data = GoogleWiFiStatus();
    print_r($data);
    $data = GoogleIPStatus("192.168.86.49");
    echo "\n\n192.168.86.49\n";
    print_r($data);
    $data = GoogleIPStatus("192.168.86.50");
    echo "\n\n192.168.86.50\n";
    print_r($data);

}
*/
//OutputJSON(UserSync::pull());
//SyncServers();
//WeMoSync::PullLightsFromHub();
echo GitHash($root_path);
?></pre>
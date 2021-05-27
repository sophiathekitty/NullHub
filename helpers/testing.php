<pre><?php
require_once("../includes/main.php");
//$servers = new Servers();
//$servers->ValidateTable();
SyncRooms();
//RemoteTasks::PullRemoteTasks();
//print_r(Tasks::LoadDueTasks());
echo clsDB::$db_g->get_err();
echo clsDB::$db_g->last_sql;
?></pre>
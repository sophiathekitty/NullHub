<?php
if(!isset($_GET['name'])) die();
require_once("../../includes/main.php");
$logs = ServicesLogs::ServiceLogs($_GET['name']);
$log_status = "ok";
$start_time = strtotime($logs['last_start']);
$done_time = strtotime($logs['last_done']);
if($start_time > $done_time){
    // is running? or not finishing?
    if($start_time - $done_time > MinutesToSeconds(5)) $log_status = "error";
    else $log_status = "running";
}
?>
<dialog class="side menu" id="service-logs-view">
    <h1>Service</h1>
    <h2><?=$logs['name'];?></h2>
    <ul>
        <li>
            <span class="key">Last Start</span>
            <span class="value"><?=date("D g:i:s a",strtotime($logs['last_start']))?></span>
        </li>
        <li>
            <span class="key">Last Done</span>
            <span class="value"><?=date("D g:i:s a",strtotime($logs['last_done']))?></span>
        </li>
        <li>
            <span class="key">Status</span>
            <span class="value"><?=$log_status?></span>
        </li>
    </ul>
    <h3>Logs</h3>
    <ul id="service-logs"><?php foreach($logs['logs'] as $log) {?>
        <li time="<?=Times24ToTime12Full($log['time']);?>"><?=$log['message'];?></li>
    <?php } ?></ul>
</dialog>

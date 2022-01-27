<?php
require_once("../../../includes/main.php");
$data = [];
if(isset($_GET['id'],$_GET['completed_by'])){
    $data['save'] = Tasks::SaveTask($_GET);
    if(!Servers::IsMain()){
        // report back to main hub
        $hub = Servers::GetMain();
        $data['url'] = $url = "http://".$hub['url']."/api/tasks/?task_id=".$_GET['id']."&completed_by=".$_GET['completed_by']."&completed=".rawurlencode($_GET['completed'])."&skipped=".rawurlencode($_GET['skipped']);
        $content=@file_get_contents($url);
        $data['remote'] = json_decode($content,true);
    }
}
$data['tasks'] = Tasks::LoadActiveTasksToday();
OutputJson($data);
?>
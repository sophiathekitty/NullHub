<?php
require_once("../../includes/main.php");
$data = [];
if(isset($_GET['task_id'],$_GET['completed_by'])){
    TaskManager::CompleteTask($task_id,$completed_by);
} else {
    $data['tasks'] = Tasks::LoadActiveTasksToday();
}
OutputJson($data);
?>
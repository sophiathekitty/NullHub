<?php
require_once("../../../includes/main.php");
$data = [];
$data['tasks'] = Tasks::LoadActiveTasksToday();
OutputJson($data);
?>
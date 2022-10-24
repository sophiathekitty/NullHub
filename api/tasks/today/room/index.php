<?php
require_once("../../../../includes/main.php");
$data = [];
$room_id = 0;
if(isset($_GET['room_id'])) $room_id = $_GET['room_id'];
else $room_id = Settings::LoadSettingsVar('room_id',0);
$data['tasks'] = Tasks::LoadActiveTasksTodayRoom($room_id);
OutputJson($data);
?>
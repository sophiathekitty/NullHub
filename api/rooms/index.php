<?php
require_once("../../includes/main.php");
$data = [];
$data['rooms'] = Rooms::AllRooms();
OutputJson($data);
?>

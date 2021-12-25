<?php
require_once("../../includes/main.php");
$data = [];
if(isset($_GET['floors'])){
    $floor = Rooms::Floor('second');
    if(count($floor['rooms'])) $data['second'] = $floor;
    $floor = Rooms::Floor("ground");
    if(count($floor['rooms'])) $data['ground'] = $floor;
    $floor = Rooms::Floor("basement");
    if(count($floor['rooms'])) $data['basement'] = $floor;
} else if(isset($_GET['floor'])){
    $data['floor'] = Rooms::Floor($_GET['floor']);
} else {
    $data['rooms'] = Rooms::AllRooms();
}
OutputJson($data);
?>

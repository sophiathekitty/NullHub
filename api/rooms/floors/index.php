<?php
require_once("../../../includes/main.php");
$data = [];
if(isset($_GET['floor'])){
    $data['floor'] = Rooms::Floor($_GET['floor']);
} else {
    $data['floors'] = [];
    $floor = Rooms::Floor('second');
    if(count($floor['rooms'])) $data['floors'][] = $floor;
    $floor = Rooms::Floor("ground");
    if(count($floor['rooms'])) $data['floors'][] = $floor;
    $floor = Rooms::Floor("basement");
    if(count($floor['rooms'])) $data['floors'][] = $floor;
}
OutputJson($data);
?>

<?php
require_once("../../../includes/main.php");
$data = [];
if(isset($_GET['room_id'])){
    $data['neighbors'] = RoomNeighbors::Neighbors($_GET['room_id']);
} else {
    $data['neighbors'] = RoomNeighbors::AllNeighbors();
}
OutputJson($data);
?>

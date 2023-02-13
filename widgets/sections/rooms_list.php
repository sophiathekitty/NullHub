<?php
require_once("../../includes/main.php");
$rooms = Rooms::ActiveRooms();
function RoomItem($room){ ?>
    <li class="room" room_id="<?=$room['id'];?>">
        <a href="#" var="name" class="key"><?=$room['name'];?></a>
        <nav class="lights"><?php
            if(HasPlugin("NullLights")) echo file_get_contents("http://localhost/plugins/NullLights/widgets/room_lights.php?room_id=".$room['id']);
        ?></nav>
        <span class="sensors"><?php
            if(HasPlugin("NullProfiles")) echo file_get_contents("http://localhost/plugins/NullProfiles/widgets/current_room_use.php?room_id=".$room['id']);
            if(HasPlugin("NullDisplay")) echo file_get_contents("http://localhost/plugins/NullDisplay/widgets/room_displays.php?room_id=".$room['id']);
            if(HasPlugin("NullSensors")) echo file_get_contents("http://localhost/plugins/NullSensors/widgets/room_temperature_bug.php?room_id=".$room['id']);
        ?></span>
        <span var="lights_on_in_room" class="bool light" val="<?=$room['lights_on_in_room'];?>"></span>
        <span var="lights_on_in_neighbors" class="bool light" val="<?=$room['lights_on_in_neighbors'];?>"></span>
    </li>
<?php } ?>
<section id="rooms_list" class="sidebar bottom">
    <h1>rooms</h1>
    <ul collection="rooms"><?php foreach($rooms as $room) RoomItem($room); ?></ul>
</section>
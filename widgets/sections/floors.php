<?php
require_once("../../includes/main.php");

function printRoom($room){ global $root_path;
    ?>
        <div class="room card" room_id="<?=$room['id'];?>" activity="<?=$room['activity'];?>" lights_on_in_room="<?=$room['lights_on_in_room'] ? "0" : "1";?>" lights_on_in_neighbors="<?=$room['lights_on_in_neighbors'] ? "0" : "1";?>" neighbors_lights_off_percent="<?=$room['neighbors_lights_off_percent'];?>">
            <h1>
                <span var="name"><?=$room['name'];?></span>
                <span class="time_sensors">
                    <span var="IsTimeForBed" val="<?=$room['IsTimeForBed'] ? "0" : "1";?>" title="<?=$room['IsTimeForBed'] ? "Is" : "Not";?> Time for Bed In <?=$room['name'];?>"></span>
                    <span var="IsBedtimeHours" val="<?=$room['IsBedtimeHours'] ? "0" : "1";?>" title="<?=$room['IsBedtimeHours'] ? "Is" : "Not";?> Bedtime Hours In <?=$room['name'];?>"></span>
                    <span var="IsTimeToGetUp" val="<?=$room['IsTimeToGetUp'] ? "0" : "1";?>" title="<?=$room['IsTimeToGetUp'] ? "Is" : "Not";?> Time To Get Up In <?=$room['name'];?>"></span>
                    <span var="IsDayInside" val="<?=$room['IsDayInside'] ? "0" : "1";?>" title="<?=$room['IsDayInside'] ? "Is" : "Not";?> Daylight Inside Of <?=$room['name'];?>"></span>
                    <span var="IsDayTime" val="<?=$room['IsDayTime'] ? "0" : "1";?>" title="<?=$room['IsDayTime'] ? "Is" : "Not";?> Daylight Outside"></span>
                </span>
                <span class="sensors"><?php
                if(HasPlugin("NullDisplay")) echo file_get_contents("http://localhost/plugins/NullDisplay/widgets/room_displays.php?room_id=".$room['id']);
                if(HasPlugin("NullSensors")) echo file_get_contents("http://localhost/plugins/NullSensors/widgets/room_temperature_bug.php?room_id=".$room['id']);
                ?></span>
            </h1>
            <div class="lights"><?php
                if(HasPlugin("NullLights")) echo file_get_contents("http://localhost/plugins/NullLights/widgets/room_lights.php?room_id=".$room['id']);
                ?></div>
            <div class="details">
                <h2>Daytime Hours</h2>
                <ul>
                    <li>
                        <span class="key">Bedtime</span>
                        <span class="val" var="bedtime" val="<?=$room['bedtime'];?>"><?=Times24ToTime12Short($room['bedtime']);?></span>
                    </li>
                    <li>
                        <span class="key">Wake up</span>
                        <span class="val" var="awake_time" val="<?=$room['awake_time'];?>"><?=Times24ToTime12Short($room['awake_time']);?></span>
                    </li>
                    <li>
                        <span class="key">Sunlight Offset</span>
                        <span class="val" var="sunlight_offset" val="<?=$room['sunlight_offset'];?>"><?=Times24ToTime12Short($room['sunlight_offset']);?></span>
                    </li>    
                    <li>
                        <span class="key">Sunrise</span>
                        <span class="val" var="sunrise" val="<?=$room['sunrise'];?>"><?=Times24ToTime12Short($room['sunrise']);?></span>
                    </li>    
                    <li>
                        <span class="key">Sunset</span>
                        <span class="val" var="sunset" val="<?=$room['sunset'];?>"><?=Times24ToTime12Short($room['sunset']);?></span>
                    </li>    
                </ul>
            </div>
            <div class="charts"><?php    
                if(HasPlugin("NullLights")) echo file_get_contents("http://localhost/plugins/NullLights/widgets/room_lights_charts.php?room_id=".$room['id']);
                if(HasPlugin("NullSensors")) echo file_get_contents("http://localhost/plugins/NullSensors/widgets/room_temperature_charts.php?room_id=".$room['id']);
            ?></div>
        </div>
    <?php
}

?>
<section id="floors" class="main">
    <div id="second_floor" level="1">
    <?php
    $rooms = Rooms::Floor("second");
    foreach($rooms['rooms'] as $room){
        printRoom($room);
    }
    ?>
    </div>
    <div id="first_floor" level="0">
    <?php
    $rooms = Rooms::Floor("ground");
    foreach($rooms['rooms'] as $room){
        printRoom($room);
    }
    ?>
    </div>
    <div id="basement_floor" level="-1">
    <?php
    $rooms = Rooms::Floor("basement");
    foreach($rooms['rooms'] as $room){
        printRoom($room);
    }
    ?>
    </div>
</section>
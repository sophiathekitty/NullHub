<?php
function RoundHour($time){
    $h = date("H",$time);
    $m = date("i",$time);
    if($m > 30) $h++;
    return $h;
}
function HourMin($time){
    return date("H:i",$time);
}
function IsDayTime(){
    $h = (int)date('H');
    $sunrise = Settings::LoadSettingsVar('sunrise_txt',"06:00");
    $sunset = Settings::LoadSettingsVar('sunrise_txt',"18:00");
    list($sunrise_h,$sunrise_m) = explode(":",$sunrise);
    list($sunset_h,$sunset_m) = explode(":",$sunset);
    if((int)$sunrise_h < $h && $h < (int)$sunset_h) {
        return true;
    }
    $m = (int)date("i");
    if((int)$sunrise_h == $h && (int)$sunrise_m < $m){
        return true;
    }
    if($h == (int)$sunset_h && $m < (int)$sunset_m) {
        return true;
    }
    return false;
}
function IsDayInside($offset = 2){
    $h = (int)date('H');
    $m = (int)date('i');
    list($sunrise_h,$sunrise_m) = explode(":",Settings::LoadSettingsVar('sunrise_txt',"06:00"));
    list($sunset_h,$sunset_m) = explode(":",Settings::LoadSettingsVar('sunrise_txt',"18:00"));

    $sunrise = (int)$sunrise_h + $offset;
    $sunset = (int)$sunset_h - $offset;
    
    $threshold = (int)Settings::LoadSettingsVar('cloud_light_threshold',75);
    if(is_null($threshold)) $threshold = 75;
    // get clouds

    $weather = WeatherLogs::CurrentWeather();
    if((int)$weather['clouds'] > ($threshold - (($offset - 1) * 10))){
        return false;
    }
    if($sunrise < $h && $h < $sunset) {
        return true;
    }
    if($sunrise == $h && $m > $sunrise_m){
        return true;
    }
    if($sunset == $h && $m < $sunset_m){
        return true;
    }
    return false;
}
function IsDayInRoom($room_id){
    $room = Rooms::RoomId($room_id);
    return IsDayInside($room['sunlight_offset']);
}

function IsMorningInRoom($room_id){
    //echo "<li>room_id: $room_id</li>";
    $room = Rooms::RoomId($room_id);
    $h = (int)date('H');
    //echo "<li>h: $h</li>";
    if(!is_null($room['awake_time'])){
        // calculate morning time based on get up time
        $sunrise = (int)date('H',strtotime($room['awake_time']));
    } else {
        // calculate morning time based on sunrise
        $sunrise = (int)LoadSettingVar('sunrise') + $room['sunlight_offset'];
    }
    //echo "<li>sunrise: $sunrise</li>";
    return ($h - 2 < $sunrise && $h + 2 > $sunrise);
}

function IsEveningInRoom($room_id){
    //echo "<li>room_id: $room_id</li>";
    $room = Rooms::RoomId($room_id);//RoomById($room_id);
    $h = (int)date('H');
    //echo "<li>h: $h</li>";
    if(!is_null($room['awake_time'])){
        // calculate morning time based on get up time
        $sunset = (int)date('H',strtotime($room['bedtime']));
    } else {
        // calculate morning time based on sunrise
        $sunset = (int)LoadSettingVar('sunset') - $room['sunlight_offset'];
    }
    //echo "<li>sunrise: $sunrise</li>";
    return ($h - 2 < $sunset && $h + 2 > $sunset);
}



function IsMorning(){
    $h = (int)date('H');
    $sunrise = (int)LoadSettingVar('sunrise');
    return ($h - 2 < $sunrise && $h + 2 > $sunrise);
}

function IsEvening(){
    //echo "<li>room_id: $room_id</li>";
    $h = (int)date('H');
    $sunset = (int)LoadSettingVar('sunset');
    return ($h - 2 < $sunset && $h + 2 > $sunset);
}

function IsWeekday(){
    $day = date("D");
    if($day == "Sat" || $day == "Sun"){
        return false;
    }
    return true;
}

?>
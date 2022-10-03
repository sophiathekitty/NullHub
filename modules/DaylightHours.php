<?php
/**
 * turns time int into rounded hour
 * @param int $time unix timestamp time int thing
 * @return int the hour
 */
function RoundHour($time){
    $h = (int)date("H",$time);
    $m = (int)date("i",$time);
    if($m > 30) $h++;
    return $h;
}
/**
 * returns the hour:min
 * @param int $time unix timestamp time int thing
 * @return string 1:23
 */
function HourMin($time){
    return date("H:i",$time);
}
/**
 * is day time (is the sun up?)
 * @depreciated use TimeOfDay::IsDayTime();
 * @return bool return true if the current time is between the sunrise and sunset
 */
function IsDayTime(){
    return TimeOfDay::IsDayTime();
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
/**
 * is day inside (offset time to shrink daylight hours)
 * @depreciated use TimeOfDay::IsDayInside() instead
 * @param int $offset the hour offset for sunrise and sunset
 * @return bool returns true if it's
 */
function IsDayInside($offset = 2){
    return TimeOfDay::IsDayInside($offset);
    $h = (int)date('H');
    $m = (int)date('i');
    list($sunrise_h,$sunrise_m) = explode(":",Settings::LoadSettingsVar('sunrise_txt',"06:00"));
    list($sunset_h,$sunset_m) = explode(":",Settings::LoadSettingsVar('sunrise_txt',"18:00"));

    $sunrise = (int)$sunrise_h + $offset;
    $sunset = (int)$sunset_h - $offset;
    
    if(HasPlugin("NullWeather")){
        $threshold = (int)Settings::LoadSettingsVar('cloud_light_threshold',75);
        if(is_null($threshold)) $threshold = 75;
        // get clouds
        $weather = WeatherLogs::CurrentWeather();
        if((int)$weather['clouds'] > ($threshold - (($offset - 1) * 10))){
            return false;
        }        
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
/**
 * is day inside room (offset time to shrink daylight hours)
 * @depreciated use TimeOfDay::IsDayInRoom($room_id) instead
 * @param int $room_id to get the hour offset for sunrise and sunset
 * @return bool returns true if it's
 */
function IsDayInRoom($room_id){
    return TimeOfDay::IsDayInRoom($room_id);
    $room = Rooms::RoomId($room_id);
    return IsDayInside($room['sunlight_offset']);
}
/**
 * is morning in room? uses awake_time's hour if awake_time set for room. or uses offset sunrise setting var
 * @depreciated use TimeOfDay::IsMorningInRoom($room_id) instead
 * @param int $room_id room id
 * @return bool true if within two hours of awake_time hour or offset sunrise hour
 */
function IsMorningInRoom($room_id){
    return TimeOfDay::IsMorningInRoom($room_id);
    //echo "<li>room_id: $room_id</li>";
    $room = Rooms::RoomId($room_id);
    $h = (int)date('H');
    //echo "<li>h: $h</li>";
    if(!is_null($room['awake_time'])){
        // calculate morning time based on get up time
        $sunrise = (int)date('H',strtotime($room['awake_time']));
    } else {
        // calculate morning time based on sunrise
        //$sunrise = (int)Settings::LoadSettingsVar('sunrise',) + $room['sunlight_offset'];
        list($sunrise) = explode(":",Settings::LoadSettingsVar('sunrise_txt',"06:00"));
        $sunrise = (int)$sunrise + $room['sunlight_offset'];
    }
    //echo "<li>sunrise: $sunrise</li>";
    return ($h - 2 < $sunrise && $h + 2 > $sunrise);
}
/**
 * is evening in room? uses awake_time's hour if bedtime set for room. or uses offset sunset setting var
 * @depreciated use TimeOfDay::IsEveningInRoom($room_id) instead
 * @param int $room_id room id
 * @return bool true if within two hours of bedtime hour or offset sunset hour
 */
function IsEveningInRoom($room_id){
    return TimeOfDay::IsEveningInRoom($room_id);
    //echo "<li>room_id: $room_id</li>";
    $room = Rooms::RoomId($room_id);//RoomById($room_id);
    $h = (int)date('H');
    //echo "<li>h: $h</li>";
    if(!is_null($room['awake_time'])){
        // calculate morning time based on get up time
        $sunset = (int)date('H',strtotime($room['bedtime']));
    } else {
        // calculate morning time based on sunrise
        list($sunset) = explode(":",Settings::LoadSettingsVar('sunrise_txt',"18:00"));
        //$sunset = (int)LoadSettingVar('sunset') - $room['sunlight_offset'];
        $sunset = (int)$sunset - $room['sunlight_offset'];
    }
    //echo "<li>sunrise: $sunrise</li>";
    return ($h - 2 < $sunset && $h + 2 > $sunset);
}


/**
 * is it currently morning based on sunrise?
 * @depreciated use TimeOfDay::IsMorning() instead
 * @return bool returns true if within two hours on either side of sunrise hour
 */
function IsMorning(){
    return TimeOfDay::IsMorning();
    $h = (int)date('H');
    //$sunrise = (int)LoadSettingVar('sunrise');
    list($sunrise) = explode(":",Settings::LoadSettingsVar('sunrise_txt',"06:00"));
    return ($h - 2 < (int)$sunrise && $h + 2 > (int)$sunrise);
}
/**
 * is it currently evening based on sunset?
 * @depreciated use TimeOfDay::IsEvening() instead
 * @return bool returns true if within two hours on either side of sunset hour
 */
function IsEvening(){
    return TimeOfDay::IsEvening();
    //echo "<li>room_id: $room_id</li>";
    $h = (int)date('H');
    //$sunset = (int)LoadSettingVar('sunset');
    list($sunset) = explode(":",Settings::LoadSettingsVar('sunrise_txt',"18:00"));
    return ($h - 2 < (int)$sunset && $h + 2 > (int)$sunset);
}
/**
 * is it a weekday?
 * @depreciated use TimeOfDay::IsWeekday() instead
 * @return bool returns true if it's a currently a weekday
 */
function IsWeekday(){
    return TimeOfDay::IsWeekday();
    $day = date("D");
    if($day == "Sat" || $day == "Sun"){
        return false;
    }
    return true;
}

?>
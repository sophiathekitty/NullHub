<?php
/**
 * static functions for figuring out what time of the day and related stuff
 */
class TimeOfDay {
    /**
     * is the current time between sunrise and sunset (uses weather data if available, otherwise defaults to 6am and 6pm)
     * @return bool will return true if after sunrise and before sunset
     */
    public static function IsDayTime(){
        $h = (int)date('H');
        $m = (int)date('i');
        $sunrise_txt = Settings::LoadSettingsVar('sunrise_txt',"6:00");
        $sunset_txt = Settings::LoadSettingsVar('sunset_txt',"18:00");
        list($sunrise,$sunrise_min) = explode(":",$sunrise_txt);
        list($sunset,$sunset_min) = explode(":",$sunset_txt);
        if($sunrise == $h){
            if($m > $sunrise_min) return true;
        }
        if($sunset == $h){
            if($m < $sunset_min) return true;
        }
        if($sunrise < $h && $h < $sunset) {
            return true;
        }
        return false;
    }
    /**
     * assumes that inside has a shorter daytime because light is coming through windows
     * @param int $offset how many hours to offset sunrise and sunset by
     * @return bool will return true if after sunrise and before sunset
     */
    public static function IsDayInside($offset = 2){
        $h = (int)date('H');
        $m = (int)date('i');
        $sunrise_txt = Settings::LoadSettingsVar('sunrise_txt',"6:00");
        $sunset_txt = Settings::LoadSettingsVar('sunset_txt',"18:00");
        list($sunrise,$sunrise_min) = explode(":",$sunrise_txt);
        list($sunset,$sunset_min) = explode(":",$sunset_txt);
        $sunrise += $offset;
        $sunset -= $offset;
        // get clouds
        if(defined("WeatherPlugin")){
            $weather = WeatherLogs::CurrentWeather();
            $threshold = (int)Settings::LoadSettingsVar('cloud_light_threshold',75);
            if((int)$weather['clouds'] > ($threshold - (($offset - 1) * 10))){
                return false;
            }    
        }
        if($sunrise == $h){
            if($m > $sunrise_min){
                return true;
            }
        }
        if($sunset == $h){
            if($m < $sunset_min){
                return true;
            }
        }
        if($sunrise < $h && $h < $sunset) {
            return true;
        }
        return false;
    }
    /**
     * uses a room's sunlight_offset to calculate the DayInside for the room
     * @param int $room_id the room to be used
     * @return bool will return true if after sunrise and before sunset
     */
    public static function IsDayInRoom($room_id){
        $room = Rooms::RoomId($room_id);
        return TimeOfDay::IsDayInside($room['sunlight_offset']);
    }
    /**
     * is within a window of time around the room's alarm time or the room's sunrise if there is no alarm set
     * @param int $room_id the room to be used
     * @return bool will return true if morning hours in room
     */
    public static function IsMorningInRoom($room_id){
        $offset = Settings::LoadSettingsVar("twilight_hour_offset",2);
        //echo "<li>room_id: $room_id</li>";
        $room = Rooms::RoomId($room_id);
        $h = (int)date('H');
        $m = (int)date('i');
        //echo "<li>h: $h</li>";
        if(!is_null($room['awake_time'])){
            // calculate morning time based on get up time
            $sunrise = (int)date('H',strtotime($room['awake_time']));
            $sunrise_min = (int)date('i',strtotime($room['awake_time']));
        } else {
            // calculate morning time based on sunrise
            $sunrise_txt = Settings::LoadSettingsVar('sunrise_txt',"6:00");
            list($sunrise,$sunrise_min) = explode(":",$sunrise_txt);
        }
        //echo "<li>sunrise: $sunrise</li>";
        if($h - $offset == $sunrise) return ($m < $sunrise_min);
        if($h + $offset == $sunrise) return ($m > $sunrise_min);
        return ($h - $offset < $sunrise && $h + $offset > $sunrise);
    }
    /**
     * is within a window of time around the room's bedtime time or the room's sunset if there is no bedtime set
     * @param int $room_id the room to be used
     * @return bool will return true if evening hours in room
     */
    public static function IsEveningInRoom($room_id){
        $offset = Settings::LoadSettingsVar("twilight_hour_offset",2);
        //echo "<li>room_id: $room_id</li>";
        $room = Rooms::RoomId($room_id);
        $h = (int)date('H');
        $m = (int)date('i');
        //echo "<li>h: $h</li>";
        if(!is_null($room['awake_time'])){
            // calculate morning time based on get up time
            $sunset = (int)date('H',strtotime($room['bedtime']));
            $sunset_min = (int)date('i',strtotime($room['bedtime']));
        } else {
            // calculate morning time based on sunrise
            $sunset_txt = Settings::LoadSettingsVar('sunset_txt',"18:00");
            list($sunset,$sunset_min) = explode(":",$sunset_txt);
        }
        //echo "<li>sunrise: $sunrise</li>";
        if($h - $offset == $sunset) return ($m < $sunset_min);
        if($h + $offset == $sunset) return ($m > $sunset_min);
        return ($h - $offset < $sunset && $h + $offset > $sunset);
    }
    /**
     * is within a window of time around sunrise
     * @return bool will return true if morning hours
     */
    public static function IsMorning(){
        $offset = Settings::LoadSettingsVar("twilight_hour_offset",2);
        $h = (int)date('H');
        $m = (int)date('i');
        $sunrise_txt = Settings::LoadSettingsVar('sunrise_txt',"6:00");
        list($sunrise,$sunrise_min) = explode(":",$sunrise_txt);
        if($h - $offset == $sunrise) return ($m < $sunrise_min);
        if($h + $offset == $sunrise) return ($m > $sunrise_min);
        return ($h - $offset < $sunrise && $h + $offset > $sunrise);
    }
        /**
     * is within a window of time around sunset
     * @return bool will return true if evening hours
     */
    public static function IsEvening(){
        $offset = Settings::LoadSettingsVar("twilight_hour_offset",2);
        //echo "<li>room_id: $room_id</li>";
        $h = (int)date('H');
        $m = (int)date('i');
        $sunset_txt = Settings::LoadSettingsVar('sunset_txt',"18:00");
        list($sunset,$sunset_min) = explode(":",$sunset_txt);
        if($h - $offset == $sunset) return ($m < $sunset_min);
        if($h + $offset == $sunset) return ($m > $sunset_min);
        return ($h - $offset < $sunset && $h + $offset > $sunset);
    }
    /**
     * figure out if today is a weekday
     * @return bool returns true if Monday through Friday and false if Saturday or Sunday
     */
    public static function IsWeekday(){
        $day = date("D");
        if($day == "Sat" || $day == "Sun"){
            return false;
        }
        return true;
    }
}
?>
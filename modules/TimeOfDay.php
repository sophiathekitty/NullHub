<?php
/**
 * static functions for figuring out what time of the day and related stuff
 */
class TimeOfDay {
    /** 
     * season
     * 
     */
    public static function Season(){
        $today = new DateTime(date("F d"));
        // get the season dates
        $spring = new DateTime('March 20');
        $summer = new DateTime('June 20');
        $fall = new DateTime('September 22');
        $winter = new DateTime('December 21');
        switch(true) {
            case $today >= $spring && $today < $summer:
                $season =  "spring";
                break;
            case $today >= $summer && $today < $fall:
                $season =  "summer";
                break;
            case $today >= $fall && $today < $winter:
                $season =  "fall";
                break;
            default:
                $season =  "winter";
        }
        return $season;
    }
    /** 
     * season
     * 
     */
    public static function Solstices(){
        $today = new DateTime(date("F d"));
        // get the season dates
        $spring = new DateTime('March 20');
        $summer = new DateTime('June 20');
        $fall = new DateTime('September 22');
        $winter = new DateTime('December 21');        
        switch(true) {
            case $today == $spring:
                $solstices =  "spring equinox";
                break;
            case $today == $summer:
                $solstices =  "summer solstice";
                break;
            case $today == $fall:
                $solstices =  "fall equinox";
                break;
            case $today == $winter:
                $solstices =  "winter solstice";
                break;
            default:
                $solstices =  "";
        }
        return $solstices;
    }
    /**
     * a string for the time of day
     * @return string the time of day (morning, day, evening, night)
     */
    public static function TimeOfDayString(){
        $time_of_day = "night";
        if(TimeOfDay::IsDayTime()) $time_of_day = "day";
        if(TimeOfDay::IsMorning()) $time_of_day = "morning";
        if(TimeOfDay::IsEvening()) $time_of_day = "evening";
        return $time_of_day;
    }
    /**
     * a string for the time of day
     * @return string the time of day (morning, day, evening, night)
     */
    public static function AfternoonBoolAsString(){
        if((int)date("G") == 23) return "false";
        if((int)date("G") >= 12) return "true";
        return "false";
    }
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
        $offset = Settings::LoadSettingsVar("twilight_hour_offset",1);
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
        $offset = Settings::LoadSettingsVar("twilight_hour_offset",1);
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
    /**
     * is the moon out?
     * @return bool returns true if there is moonrise data and the moon is out
     */
    public static function MoonOut(){
        $moonrise = Settings::LoadSettingsVar('moonrise_txt');
        $moonset = Settings::LoadSettingsVar('moonset_txt');
        list($rise_hour,$rise_min) = explode(":",$moonrise);
        list($set_hour,$set_min) = explode(":",$moonset);
        $h = (int)date('H');
        $m = (int)date('i');
        if($rise_hour > $set_hour){
            if($h < $set_hour) return true;
            $set_hour += 24;
        }
        if($h > $rise_hour && $h < $set_hour) return true;
        if($h == $rise_hour && $m > $rise_min) return true;
        if($h == $set_hour && $m < $set_min) return true;    
        return false;
    }
    /**
     * is the moon out?
     * @return bool returns true if there is moonrise data and the moon is out
     */
    public static function MoonOutBoolAsInt(){
        if(TimeOfDay::MoonOut()) return 1;
        return 0;
    }
    /**
     * moon phase as string
     * @return bool returns moon phase as string
     */
    public static function MoonPhaseString(){
        $moon_phase = Settings::LoadSettingsVar("moon_phase","0");
        $phase = "new moon";
        if($moon_phase < 1) $phase = "waning crescent";
        if($moon_phase < 0.75) $phase = "waning gibbous";
        if($moon_phase < 0.50) $phase = "waxing gibbous";
        if($moon_phase < 0.25) $phase = "waxing crescent";
        if($moon_phase == 0.25) $phase = "first quarter";
        if($moon_phase == 0.50) $phase = "full moon";
        if($moon_phase == 0.75) $phase = "last quarter";
        if($moon_phase == 0 || $moon_phase == 1) $phase = "new moon";
        return $phase;
    }

}
?>
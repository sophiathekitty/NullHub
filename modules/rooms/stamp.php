<?php
class RoomStamp {
    
    public function Stamp($room){
        $room['IsTimeToGetUp'] = $this->IsTimeToGetUp($room);
        $room['IsTimeForBed'] = $this->IsTimeForBed($room);
        $room['IsBedtimeHours'] = $this->IsBedtimeHours($room);
        $room['IsDayInside'] = $this->IsDayInside($room);
        $room['IsDaytime'] = $this->IsDaytime($room);

    }
    private function IsTimeToGetUp($room){
        return (
            time() > strtotime($room['awake_time'])-HoursToSeconds(Settings::LoadSettingsVar('awake_time_before_hours',2)) &&
            time() < strtotime($room['awake_time'])+HoursToSeconds(Settings::LoadSettingsVar('awake_time_after_hours',2))
        );
    }
    private function IsTimeForBed($room){
        return (
            time() > strtotime($room['bedtime'])-HoursToSeconds(Settings::LoadSettingsVar('bedtime_before_hours',2)) &&
            time() < strtotime($room['bedtime'])+HoursToSeconds(Settings::LoadSettingsVar('bedtime_after_hours',2))
        );
    }
    private function IsBedtimeHours($room){
        if($room['bedtime'] > $room['awake_time']){
            return (
                time() > strtotime($room['bedtime']) ||
                time() < strtotime($room['awake_time'])
            );    
        }
        return (
            time() > strtotime($room['bedtime']) &&
            time() < strtotime($room['awake_time'])
        );
    }
    private function IsDayInside($room){
        $sunrise = (int)$room['sunrise'];
        if($sunrise < 10) $sunrise = "0$sunrise";
        $sunrise = "$sunrise:00:00";
        $sunset = (int)$room['sunset'];
        if($sunset < 10) $sunset = "0$sunset";
        $sunset = "$sunset:00:00";
        return (
            time() > strtotime($sunrise) &&
            time() < strtotime($sunset)
        );
    }
    private function IsDaytime($room){
        $sunrise = (int)$room['sunrise'];
        if($sunrise < 10) $sunrise = "0$sunrise";
        $sunrise = "$sunrise:00:00";
        $sunset = (int)$room['sunset'];
        if($sunset < 10) $sunset = "0$sunset";
        $sunset = "$sunset:00:00";
        return (
            time() > strtotime($sunrise)-HoursToSeconds($room['sunlight_offset']) &&
            time() < strtotime($sunset)+HoursToSeconds($room['sunlight_offset'])
        );
    }
}
?>
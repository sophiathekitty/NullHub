<?php
function IsBedtimeRoom($room_id){
    $room = Rooms::RoomId($room_id);
    
    if(!is_null($room['bedtime']) && !is_null($room['awake_time'])){
        if($room['bedtime'] < $room['awake_time']){
            if(date("H:i:s") >= $room['bedtime'] && date("H:i:s") < $room['awake_time']){
                return true;
            }
        } else {
            if(date("H:i:s") >= $room['bedtime'] || date("H:i:s") < $room['awake_time']){
                return true;
            }    
        }
    }
    return false;
}
function IsBedtimeUser($user_id){
    $users = new Users();
    $user = $users->LoadById($user_id);
    if(!is_null($user['bedtime']) && !is_null($user['awake_time'])){
        if($user['bedtime'] > $user['awake_time']){
            if(date("H:i:s") > $user['bedtime'] || date("H:i:s") < $user['awake_time']){
                return true;
            }
        } else {
            if(date("H:i:s") > $user['bedtime'] && date("H:i:s") < $user['awake_time']){
                return true;
            }    
        }
    }
    return false;
}
function IsTimeForBedRoom($room_id){
    $room = Rooms::RoomId($room_id);
    if(!is_null($room['bedtime'])){
        list($hour,$min,$sec) = explode(':',$room['bedtime']);
        $bedtime_hour = (int)$hour;
        $bedtime_min = (int)$min;
        $current_hour = (int)date("H");
        $current_min = (int)date("i");
        $bedtime_start_hour = $bedtime_hour - 5;
        if($bedtime_start_hour < 0) $bedtime_start_hour += 24;
        if($current_hour > $bedtime_hour - 5 && $current_hour < $bedtime_hour + 1){
            return true;
        }
        if($current_hour == $bedtime_hour - 5 && $current_min >= $bedtime_min){
            return true;
        }    
        if($current_hour > $bedtime_start_hour && $current_hour < $bedtime_start_hour + 6){
            return true;
        }
        if($current_hour == $bedtime_start_hour && $current_min >= $bedtime_min){
            return true;
        }    
        return false;
    }
}
function IsTimeForToGetUpRoom($room_id){
    $room = Rooms::RoomId($room_id);
    if(!is_null($room['awake_time'])){
        list($hour,$min,$sec) = explode(':',$room['awake_time']);
        $awake_time_hour = (int)$hour;
        $awake_time_min = (int)$min;
        $current_hour = (int)date("H");
        $current_min = (int)date("i");
        if($current_hour > $awake_time_hour - 2 && $current_hour < $awake_time_hour + 2){
            return true;
        }
        if($current_hour == $awake_time_hour - 2 && $current_min >= $awake_time_min){
            return true;
        }    
        return false;
    }
}
?>
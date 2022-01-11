<?php
/**
 * is bedtime in room? is the room in sleep times?
 * @param int $room_id the room id 
 * @return bool returns true if room has bedtime and awake_time and is between the betime and awake_time
 */
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
/**
 * is bedtime for user? should the user be asleep now?
 * @param int $user_id the user id
 * @return bool returns true if user has bedtime and awake_time and is between the betime and awake_time
 */
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
/**
 * is it time for bed in the room? is the room shutting down?
 * @param int $room_id the room id
 * @return bool return true if room has bedtime and within the 5 hours before the bedtime
 */
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
/**
 * is time to get up in room? is it morning time in the room?
 * @param int $room_id the room id
 * @return bool return true if has awake_time and is 2 hours before or after awake_time
 */
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
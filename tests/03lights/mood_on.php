<?php
if(HasPlugin("NullLights")){
    // make sure fake data exists
    if(!defined("lights_fake_data")) CreateLightsTestData();
    // run test
    $light6 = WeMoLights::MacAddress('room4:mood'); // on 5 minutes
    $light2 = WeMoLights::MacAddress('room1:mood'); // off 5 minutes
    $on_min = round(SecondsToMinutes(RoomLightTime::RoomMoodOnTime($light6)));
    $off_min = round(SecondsToMinutes(RoomLightTime::RoomMoodOffTime($light2)));
    $off_min_zero = round(SecondsToMinutes(RoomLightTime::RoomMoodOffTime($light6)));
    $on_min_old = round(SecondsToMinutes(RoomMoodOnTime($light6['room_id'],MinutesToSeconds(7))));
    $off_min_old = round(SecondsToMinutes(RoomMoodOffTime($light2['room_id'],MinutesToSeconds(7))));
    Debug::Log("light 6 on minutes",$on_min);
    Debug::Log("light 2 off minutes",$off_min);
    Debug::Log("light 6 off minutes",$off_min_zero);
    Debug::Log("light 6 on (legacy) minutes",$on_min_old);
    Debug::Log("light 2 off (legacy) minutes",$off_min_old);
    if($on_min == 5 && $off_min_zero == 0 && $on_min == $off_min && $on_min == $on_min_old && $off_min == $off_min_old) $tests['running'][$test_name] = "Pass";
    else {
        $tests['running'][$test_name] = [];
        $tests['running'][$test_name]["RoomLightTime::RoomMoodOnTime"] = ($on_min == 5) ? "Pass" : "-Fail";
        $tests['running'][$test_name]["RoomLightTime::RoomMoodOffTime"] = ($off_min == 5) ? "Pass" : "-Fail";
        $tests['running'][$test_name]["RoomLightTime::RoomMoodOffTime(false positive)"] = ($off_min_zero == 0) ? "Pass" : "-Fail";
        $tests['running'][$test_name]["RoomMoodOnTime"] = ($on_min_old == 5) ? "Pass" : "-Fail";
        $tests['running'][$test_name]["RoomMoodOffTime"] = ($off_min_old == 5) ? "Pass" : "-Fail";
    }
} else {
    $tests['running'][$test_name] = "Skipped";
}
?>
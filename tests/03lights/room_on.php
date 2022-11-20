<?php
if(HasPlugin("NullLights")){
    // make sure fake data exists
    if(!defined("lights_fake_data")) CreateLightsTestData();
    // run test
    $light1 = WeMoLights::MacAddress('room1:lamp'); // on 5 minutes
    $light4 = WeMoLights::MacAddress('room3:lamp'); // off 5 minutes
    $on_min = round(SecondsToMinutes(WemoTime::RoomOnTime($light1)));
    $off_min_zero = round(SecondsToMinutes(WemoTime::RoomOffTime($light1)));
    $off_min = round(SecondsToMinutes(WemoTime::RoomOffTime($light4)));
    $off_min_old = round(SecondsToMinutes(RoomOffTime($light4['room_id'],MinutesToSeconds(7))));
    Debug::Log("light 1 on minutes",$on_min);
    Debug::Log("light 1 off minutes",$off_min_zero);
    Debug::Log("light 4 off minutes",$off_min);
    Debug::Log("light 4 off (legacy) minutes",$off_min_old);
    if($on_min == 5 && $off_min_zero == 0 && $on_min == $off_min && $off_min == $off_min_old) $tests['running'][$test_name] = "Pass";
    else {
        $tests['running'][$test_name] = [];
        $tests['running'][$test_name]["WemoTime::RoomOnTime"] = ($on_min == 5) ? "Pass" : "-Fail";
        $tests['running'][$test_name]["WemoTime::RoomOffTime(false positive)"] = ($off_min_zero == 0) ? "Pass" : "-Fail";
        $tests['running'][$test_name]["WemoTime::RoomOffTime"] = ($off_min == 5) ? "Pass" : "-Fail";
        $tests['running'][$test_name]["RoomOffTime"] = ($off_min_old == 5) ? "Pass" : "-Fail";
    }
} else {
    $tests['running'][$test_name] = "Skipped";
}
?>
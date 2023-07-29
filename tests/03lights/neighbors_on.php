<?php
if(HasPlugin("NullLights")){
    // make sure fake data exists
    if(!defined("lights_fake_data")) CreateLightsTestData();
    // run test
    $light3 = WeMoLights::MacAddress('room2:ambient'); // on 5 minutes
    $light5 = WeMoLights::MacAddress('room4:lamp'); // off 5 minutes
    $on_min = round(SecondsToMinutes(RoomLightTime::NeighborsOnTime($light3)));
    $off_min = round(SecondsToMinutes(RoomLightTime::NeighborsOffTime($light5)));
    $off_min_zero = round(SecondsToMinutes(RoomLightTime::NeighborsOffTime($light3)));
    //$on_min_old = round(SecondsToMinutes(NeighborsOn($light3['room_id'],MinutesToSeconds(7))));
    $off_min_old = round(SecondsToMinutes(NeighborsOffTime($light5['room_id'],MinutesToSeconds(7))));
    Debug::Log("light 3 neighbors on minutes",$on_min);
    Debug::Log("light 5 neighbors off minutes",$off_min);
    Debug::Log("light 3 neighbors off minutes",$off_min_zero);
    //Debug::Log("light 3 neighbors on (legacy) minutes",$on_min_old);
    Debug::Log("light 5 neighbors off (legacy) minutes",$off_min_old);
    if($on_min == 5 && $off_min_zero == 0 && $on_min == $off_min && $off_min == $off_min_old) $tests['running'][$test_name] = "Pass";
    else {
        $tests['running'][$test_name] = [];
        $tests['running'][$test_name]["RoomLightTime::NeighborsOnTime"] = ($on_min == 5) ? "Pass" : "-Fail";
        $tests['running'][$test_name]["RoomLightTime::NeighborsOffTime"] = ($off_min == 5) ? "Pass" : "-Fail";
        $tests['running'][$test_name]["RoomLightTime::NeighborsOffTime(false positive)"] = ($off_min_zero == 0) ? "Pass" : "-Fail";
        $tests['running'][$test_name]["NeighborsOffTime"] = ($off_min_old == 5) ? "Pass" : "-Fail";
    }
} else {
    $tests['running'][$test_name] = "Skipped";
}
?>
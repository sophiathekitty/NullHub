<?php
if(HasPlugin("NullLights")){
    // make sure fake data exists
    if(!defined("lights_fake_data")) CreateLightsTestData();
    // run test
    $light1 = WeMoLights::MacAddress('room1:lamp'); // on 5 minutes
    $on_min = round(SecondsToMinutes(RoomLightTime::OnDuringTime($light1,MinutesToSeconds(9))));
    $on_min_old = round(SecondsToMinutes(WeMoOnTime($light1['mac_address'],MinutesToSeconds(9))));
    Debug::Log("light 1 on minutes during the last 9 minutes",$on_min);
    Debug::Log("light 1 on (legacy) minutes during the last 9 minutes",$on_min_old);
    if($on_min == 5 && $on_min == $on_min_old) $tests['running'][$test_name] = "Pass";
    else {
        $tests['running'][$test_name] = [];
        $tests['running'][$test_name]["RoomLightTime::OnDuringTime"] = ($on_min == 5) ? "Pass" : "-Fail";
        $tests['running'][$test_name]["WeMoOnNowMinutes"] = ($on_min_old == 5) ? "Pass" : "-Fail";
    }
} else {
    $tests['running'][$test_name] = "Skipped";
}
?>
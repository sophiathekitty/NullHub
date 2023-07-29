<?php
if(HasPlugin("NullLights")){
    // make sure fake data exists
    if(!defined("lights_fake_data")) CreateLightsTestData();
    // run test
    $light1 = WeMoLights::MacAddress('room1:lamp'); // on 5 minutes
    $light2 = WeMoLights::MacAddress('room1:mood'); // off 5 minutes
    $on_min = round(RoomLightTime::OnMinutes($light1));
    $off_min_zero = round(RoomLightTime::OffMinutes($light1));
    $off_min = round(RoomLightTime::OffMinutes($light2));
    $on_min_old = round(WeMoOnNowMinutes($light1['mac_address']));
    $off_min_old = round(WeMoOffNowMinutes($light2['mac_address']));
    Debug::Log("light 1 on minutes",$on_min);
    Debug::Log("light 1 off minutes",$off_min_zero);
    Debug::Log("light 2 off minutes",$off_min);
    Debug::Log("light 1 on (legacy) minutes",$on_min_old);
    Debug::Log("light 2 off (legacy) minutes",$off_min_old);
    if($on_min == 5 && $off_min_zero == 0 && $on_min == $off_min && $on_min == $on_min_old && $off_min == $off_min_old) $tests['running'][$test_name] = "Pass";
    else {
        $tests['running'][$test_name] = [];
        $tests['running'][$test_name]["RoomLightTime::OnMinutes"] = ($on_min == 5) ? "Pass" : "-Fail";
        $tests['running'][$test_name]["RoomLightTime::OffMinutes"] = ($off_min == 5) ? "Pass" : "-Fail";
        $tests['running'][$test_name]["RoomLightTime::OffMinutes(false positive)"] = ($off_min == 0) ? "Pass" : "-Fail";
        $tests['running'][$test_name]["WeMoOnNowMinutes"] = ($on_min_old == 5) ? "Pass" : "-Fail";
        $tests['running'][$test_name]["WeMoOffNowMinutes"] = ($off_min_old == 5) ? "Pass" : "-Fail";
    }
} else {
    $tests['running'][$test_name] = "Skipped";
}
?>
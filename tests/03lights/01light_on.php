<?php
if(HasPlugin("NullLights")){
    // make sure fake data exists
    if(!defined("lights_fake_data")) CreateLightsTestData();
    // run test
    $light1 = WeMoLights::MacAddress('room1:lamp');
    $light2 = WeMoLights::MacAddress('room1:mood');
    $on_min = WemoTime::OnMinutes($light1);
    $off_min = WemoTime::OffMinutes($light2);
    Debug::Log("light 1 on minutes",$on_min);
    Debug::Log("light 2 off minutes",$off_min);
    if($on_min == 5 && $on_min == $off_min) $tests['running'][$test_name] = "Pass";
} else {
    $tests['running'][$test_name] = "Skipped";
}
?>
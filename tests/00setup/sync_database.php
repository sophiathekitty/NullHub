<?php
// tests
//if(!defined("SETUP_MODE")) define("SETUP_MODE","testing");
global $device_info, $root_path;
if(isset($device_info)){
    if(SetupInstallDatabase() == "database installed and synced"){
        $tests['running'][$test_name] = "Pass";
    }
    Debug::Log(Servers::GetMain());
    Debug::Log(Servers::GetHub());
}
?>
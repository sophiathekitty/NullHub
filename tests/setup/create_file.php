<?php
// tests
if(!defined("SETUP_MODE")) define("SETUP_MODE","testing");
global $device_info, $root_path;
if(isset($device_info)){
    CreateSettingsFile($device_info);
    if(is_file($root_path."settings_test.php")){
        $tests['running'][$test_name] = "Pass";
        unlink($root_path."settings_test.php");
        if(is_file($root_path."settings_test.php"))
            $tests['running'][$test_name] = "Fail";
    } 
}
?>
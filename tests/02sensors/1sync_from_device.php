<?php
if(HasPlugin("NullSensors")){
    if(!defined("sensors_sync_from_hub")) PullRemoteSensors::Sync();
    define("practice_sync_from_device",true);
    $sensors = TemperatureSensors::LoadSensors();
    foreach($sensors as $sensor){
        $sensor['temp'] = 0;
        TemperatureSensors::SaveSensor($sensor);
    }
    PullRemoteSensors::Sync();
    $sensors = TemperatureSensors::LoadSensors();
    $changed = 0;
    foreach($sensors as $sensor){
        if($sensor['temp'] != 0) $changed++;
    }
    if($changed > 0) $tests['running'][$test_name] = "Pass";
} else {
    $tests['running'][$test_name] = "Skipped";
}

?>
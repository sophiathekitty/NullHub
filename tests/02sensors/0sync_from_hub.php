<?php
if(HasPlugin("NullSensors")){
    $hub = Servers::GetMain();
    if($hub['mac_address'] == "device_info") {
        SyncServers();
        SyncRooms();
    }
    define("sensors_sync_from_hub",true);
    PullRemoteSensors::Sync();
    $sensors = TemperatureSensors::LoadSensors();
    if(count($sensors)) $tests['running'][$test_name] = "Pass";
} else {
    $tests['running'][$test_name] = "Skipped";
}
?>
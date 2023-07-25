<?php
//echo "Hello ";
//define("DEBUG","ECHO");
require_once("../includes/main.php");
//echo " World\n";
/*
//$model = new clsModel();
$data = [];
$pallet = GenerateColorPallet("#6ca9c9",7,1);
$i = 0;
$days = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
foreach($pallet as $color){
    //$i++;
    SetHubColor($days[$i++],$color,"calendar");
}
$data['days'] = $pallet;

$pallet = GenerateColorPallet("#8900d3",11,-1);
$i = 0;
foreach($pallet as $color){
    if($i == 0) SetHubColor("temp_$i",$color,"weather");
    $i++;
    SetHubColor("temp_$i",$color,"weather");
}
$data['temp'] = $pallet;

$pallet = GenerateColorPallet("#414dcc",12,-1);
$i = 0;
foreach($pallet as $color){
    $i++;
    SetHubColor("month_$i",$color,"calendar");
}
$data['months'] = $pallet;
*/
//$data = GoveeAPI::DeviceList();
//Govee::FindDevices();
//Govee::Observe();
/*
$govees = GoveeLights::AllLights();
foreach($govees as $govee){
    $govee['target_state'] = 1;
    Govee::SetState($govee);
}
*/
//if(isset($_GET['state'])) LightGroups::SetState(22,$_GET['state']);
//else LightGroups::SetState(22,1);
/*
try {
    $data = [];
    // Code that may throw an exception or error
    SomeCass::FakeFunction();
} catch (Exception $e) {
    // This block will handle exceptions of type Exception (or its subclasses)
    $data['exception'] = "An exception occurred: " . $e->getMessage();
} catch (Error $e) {
    // This block will handle fatal errors, parse errors, and recoverable errors (PHP 7.0 - 7.3)
    $data['error'] = "An error occurred: " . $e->getMessage();
} catch (RuntimeException $e) {
    // Represents errors that occur during runtime, often indicating programming errors or unexpected conditions.
    $data['runtime_exception'] = "An error occurred: " . $e->getMessage();
}
*/
OutputJson($data);
?>
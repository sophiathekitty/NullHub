<?php
//echo "Hello ";
//define("DEBUG","ECHO");
require_once("../includes/main.php");
//echo " World\n";

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

OutputJson($data);
?>
<?php

function ServerType(){
    global $root_path;
    $type = "device";
    $plugins = FindPluginsLocal($root_path."plugins/");
    $extensions = FindLocalExtensions();
    sort($plugins);
    sort($extensions);
    $installed = implode("",$plugins).implode("",$extensions);
    
    if(
        str_contains($installed,"NullLights") && 
        str_contains($installed,"NullSensors") && 
        str_contains($installed,"NullWeather") && 
        str_contains($installed,"MealPlanner") && 
        str_contains($installed,"NullProfiles")
        ){
        $type = "hub";
    } else if($installed == "NullDisplay/NullWeather/MealPlanner") {
        $type = "eInk display";
    } else if($installed == "NullDisplay/NullWeather/") {
        $type = "micro display";
    } else if($installed == "NullSensors/") {
        $type = "thermometer";
    }
    //NullDisplay/NullLights/NullProfiles/NullSensors/NullWeather/MealPlanner
    //$type = $installed;

    return $type;
}
function str_contains ($haystack, $needle){
    return (strpos($haystack, $needle) !== false);
}
?>
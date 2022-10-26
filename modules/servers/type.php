<?php
/**
 * figure out what type of server this is based on which plugins and extensions
 * are currently installed
 */
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
    } else if($installed == "NullDisplay/NullWeather/" || "NullDisplay/NullSensors/NullWeather/") {
        $type = "micro display";
    } else if($installed == "NullSensors/") {
        $type = "thermometer";
    }
    //NullDisplay/NullLights/NullProfiles/NullSensors/NullWeather/MealPlanner
    //$type = $installed;

    return $type;
}
/**
 * see if a string contains a substring
 * @param string $haystack the string to search
 * @param string $needle the string to search for in the $haystack
 * @return bool returns true if $haystack contains $needle otherwise false
 */
function str_contains ($haystack, $needle){
    return (strpos($haystack, $needle) !== false);
}
?>
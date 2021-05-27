<?php
require_once("../includes/main.php");

if(eInkNeedsRefresh()){
    echo "Refresh?<br>";
    echo shell_exec("python3 /var/www/html/python/eInk/refresh.py");
    echo "why no work?<br>";

    /*
    Settings::SaveSettingsVar('eInk_date',date("l"));
    $url = "http://localhost/plugins/NullWeather/api/weather/live";
    $info = file_get_contents($url);
    $data = json_decode($info,true);
    Settings::SaveSettingsVar('eInk_weather_icon',$data['weather']['icon']);
    $url = "http://localhost/extensions/MealPlanner/api/meal/";
    $info = file_get_contents($url);
    $data = json_decode($info,true);
    Settings::SaveSettingsVar('eInk_recipe',$data['today']['recipe']['name']);
    if($data['today']['side_id'] == 0){
        // no side
        Settings::SaveSettingsVar('eInk_side'," ");
    } else {
        Settings::SaveSettingsVar('eInk_side',$data['today']['side']['name']);
    }
    */
}

function eInkNeedsRefresh(){
    $eInk_date = Settings::LoadSettingsVar('eInk_date');
    if(is_null($eInk_date)) return true;
    if($eInk_date != date("l")) return true;
    
    $eInk_weather_icon = Settings::LoadSettingsVar('eInk_weather_icon');
    $url = "http://localhost/plugins/NullWeather/api/weather/live";
    $info = file_get_contents($url);
    $data = json_decode($info,true);
    if($eInk_weather_icon  != $data['weather']['icon']) return true;
    
    $eInk_recipe = Settings::LoadSettingsVar('eInk_recipe');
    $eInk_side = Settings::LoadSettingsVar('eInk_side');
    $url = "http://localhost/extensions/MealPlanner/api/meal/";
    $info = file_get_contents($url);
    $data = json_decode($info,true);
    if($eInk_recipe != $data['today']['recipe']['name']) return true;
    if($data['today']['side_id'] == 0){
        // no side
        if($eInk_side != " ") return true;
    } else {
        if($eInk_side != $data['today']['side']['name']) return true;
    }

    return false;
}
?>
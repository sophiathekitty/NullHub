<?php
require_once("../../includes/main.php");
$data = [];
if(!isset($_GET['simple'])){
    $data['NullWeather'] = HasPlugin("NullWeather");
    if($data['NullWeather']){
        try{
            $data['weather'] = WeatherLogs::CurrentWeather();
        } catch(Exception $e){
            Debug::Log($e);
        }
    }
    $data['NullSensors'] = HasPlugin("NullSensors");
    if($data['NullSensors']){
        try{
            $data['indoors'] = AverageIndoorTemperature();
        } catch(Exception $e){
            Debug::Log($e);
        }
    }
    $data['clouds'] = Settings::LoadSettingsVar("clouds","0");
    $data['moonrise'] = Settings::LoadSettingsVar("moonrise_txt","18:00");
    $data['moonset'] = Settings::LoadSettingsVar("moonset_txt","6:00");
    $data['moon_phase'] = Settings::LoadSettingsVar("moon_phase","0");
    $data['moon_out'] = 0;
    if(TimeOfDay::MoonOut()) $data['moon_out'] = 1;
    $data['sun_out'] = 0;
    if(TimeOfDay::SunOut()) $data['sun_out'] = 1;

    $today = new DateTime(date("F d"));
    // get the season dates
    $spring = new DateTime('March 20');
    $summer = new DateTime('June 20');
    $fall = new DateTime('September 22');
    $winter = new DateTime('December 21');
    switch(true) {
        case $today >= $spring && $today < $summer:
            $data['season'] =  "spring";
            break;
        case $today >= $summer && $today < $fall:
            $data['season'] =  "summer";
            break;
        case $today >= $fall && $today < $winter:
            $data['season'] =  "fall";
            break;
        default:
            $data['season'] =  "winter";
    }
    switch(true) {
        case $today == $spring:
            $data['solstices '] =  "spring equinox";
            break;
        case $today == $summer:
            $data['solstices '] =  "summer solstice";
            break;
        case $today == $fall:
            $data['solstices '] =  "fall equinox";
            break;
        case $today == $winter:
            $data['solstices '] =  "winter solstice";
            break;
        default:
            $data['solstices '] =  "";
    }
    $data["day_of_week"] = date("D");
    $data["month"] = date("M");
}
$data['sunrise'] = Settings::LoadSettingsVar("sunrise_txt","6:00");
$data['sunset'] = Settings::LoadSettingsVar("sunset_txt","18:00");
$data['time_of_day'] = "night";
if(TimeOfDay::IsDayTime()) $data['time_of_day'] = "day";
if(TimeOfDay::IsMorning()) $data['time_of_day'] = "morning";
if(TimeOfDay::IsEvening()) $data['time_of_day'] = "evening";

OutputJson($data);
?>
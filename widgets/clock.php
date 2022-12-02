<?php 
require_once("../includes/main.php");
$moon_percent = 0;
$sun_percent = 0.5;
if(HasPlugin("NullWeather")) { 
    $weather = WeatherLogs::CurrentWeather();
    $daytime = Sunrise::LoadToday();
    $moon_percent = $daytime['moon_percent'];
    $sun_percent = $daytime['sun_percent'];
}
$moon_deg = (260*$moon_percent)-20;
$sun_deg = (130*$sun_percent)+35;
$moon_scale = 3.5*abs($moon_percent-0.5)+1;
$sun_scale = 4*abs($sun_percent-0.5)+1;
if($moon_scale < 1) $moon_scale = 1;
if($sun_scale < 1) $sun_scale = 1;
if(HasPlugin("NullSensors")) $indoors = AverageIndoorTemperature();
?>
<div id="clock" time="<?=date("g:i");?>" time_of_day="<?=TimeOfDay::TimeOfDayString()?>" day="<?=date("D");?>" month="<?=date("M");?>" season="<?=TimeOfDay::Season()?>" clouds="<?=Settings::LoadSettingsVar("clouds","0");?>" moon_visible="<?=TimeOfDay::MoonOutBoolAsInt();?>" NullWeather="<?=HasPluginBoolAsString("NullWeather")?>" NullSensors="<?=HasPluginBoolAsString("NullSensors")?>" class="clock">
    <div class="sky" moon_phase="<?=TimeOfDay::MoonPhaseString();?>" afternoon="<?=TimeOfDay::AfternoonBoolAsString();?>">
        <div id="sun_holder" style="transform: rotate(<?=$sun_deg;?>deg);"><div class="sun"  style="width:<?=$sun_scale?>em; height:<?=$sun_scale?>em;"></div></div>
        <div id="moon_holder" style="transform: rotate(<?=$moon_deg;?>deg);"><div class="moon" phase="<?=Settings::LoadSettingsVar("moon_phase","0");?>" stage="<?=TimeOfDay::MoonPhaseString();?>" style="width:<?=$moon_scale?>em; height:<?=$moon_scale?>em; transform: rotate(<?=($moon_deg*-1);?>deg);"><div class="disc"></div></div></div>
        <div class="clouds"></div>
        <?php if(HasPlugin("NullWeather")) { ?><div class="current_weather" var="icon" icon="<?=$weather['icon'];?>" main="<?=$weather['main'];?>"></div><?php } ?>
    </div>
    <div var="time" unit="<?=date("a");?>"><?=date("g:i");?></div>
    <?php if(HasPlugin("NullWeather")) { ?><div class="temp outdoors NullWeather" var="temp" unit="fahrenheit" style="color:<?=interpolateColor(Colors::GetColor("temp_".floor($weather['temp']/10)),Colors::GetColor("temp_".ceil($weather['temp']/10)),($weather['temp']/10)-floor($weather['temp']/10));?>"><?=$weather['temp'];?></div><?php } ?>
    <div var="day_of_week"><?=date("D");?></div>
    <div class="slideshow" var="date"><?=date("M j, Y");?></div>
    <div class="slideshow" var="sunrise" unit=""><?=Times24ToTime12Short(Settings::LoadSettingsVar("sunrise_txt","6:00"))?></div>
    <div class="slideshow" var="sunset" unit=""><?=Times24ToTime12Short(Settings::LoadSettingsVar("sunset_txt","18:00"))?></div>
    <?php if(HasPlugin("NullWeather")) { ?><div class="temp extra NullWeather" var="feels_like" unit="fahrenheit" style="color:<?=interpolateColor(Colors::GetColor("temp_".floor($weather['feels_like']/10)),Colors::GetColor("temp_".ceil($weather['feels_like']/10)),($weather['feels_like']/10)-floor($weather['feels_like']/10));?>"><?=$weather['feels_like'];?></div>
    <div class="humidity extra NullWeather" var="humidity" unit="percent" style="color:<?=interpolateColor(Colors::GetColor("hum_0"),Colors::GetColor("hum_1"),$weather['humidity']/100);?>"><?=$weather['humidity'];?></div>
    <div class="wind extra NullWeather" var="wind_speed" unit="MetersPerSeconds" style="color:<?=interpolateColor(Colors::GetColor("wind_0"),Colors::GetColor("wind_1"),$weather['wind_speed']/100);?>"><?=$weather['wind_speed'];?></div>
    <div class="description extra NullWeather" var="description"><?=$weather['description'];?></div>
    <?php echo file_get_contents("http://localhost/plugins/NullWeather/widgets/outdoor_temp_chart.php"); } ?>
    <?php if(HasPlugin("NullSensors")) { echo file_get_contents("http://localhost/plugins/NullSensors/widgets/indoor_temperature_chart.php"); ?>
    <div class="temp indoors NullSensors" var="temp" unit="fahrenheit" style="color:<?=interpolateColor(Colors::GetColor("temp_".floor($indoors['temp']/10)),Colors::GetColor("temp_".ceil($indoors['temp']/10)),($indoors['temp']/10)-floor($indoors['temp']/10));?>"><?=round($indoors['temp']);?></div>
    <div class="humidity indoors NullSensors" var="hum" unit="percent" style="color:<?=interpolateColor(Colors::GetColor("hum_0"),Colors::GetColor("hum_1"),$indoors['hum']/100);?>"><?=round($indoors['hum']);?></div><?php } ?>
</div>
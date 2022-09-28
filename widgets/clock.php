<?php 
require_once("../includes/main.php");
if(HasPlugin("NullWeather")) $weather = WeatherLogs::CurrentWeather();
?>
<div id="clock" time="<?=date("g:i");?>" time_of_day="<?=TimeOfDay::TimeOfDayString()?>" day="<?=date("D");?>" month="<?=date("M");?>" season="<?=TimeOfDay::Season()?>" clouds="<?=Settings::LoadSettingsVar("clouds","0");?>" moon_visible="<?=TimeOfDay::MoonOutBoolAsInt();?>" NullWeather="<?=HasPluginBoolAsString("NullWeather")?>" NullSensors="<?=HasPluginBoolAsString("NullSensors")?>" class="clock">
    <div class="sky" moon_phase="<?=TimeOfDay::MoonPhaseString();?>" afternoon="<?=TimeOfDay::AfternoonBoolAsString();?>">
        <div class="sun"></div>
        <div class="moon" phase="<?=Settings::LoadSettingsVar("moon_phase","0");?>" stage="<?=TimeOfDay::MoonPhaseString();?>"><div class="disc"></div></div>
        <div class="clouds"></div>
        <?php if(HasPlugin("NullWeather")) { ?><div class="current_weather" var="icon" icon="<?=$weather['icon'];?>" main="<?=$weather['main'];?>"></div><?php } ?>
    </div>
    <div var="time" unit="<?=date("a");?>"><?=date("g:i");?></div>
    <?php if(HasPlugin("NullWeather")) { ?><div class="temp outdoors NullWeather" var="temp" unit="fahrenheit" style="color:<?=interpolateColor(Colors::GetColor("temp_".floor($weather['temp']/10)),Colors::GetColor("temp_".ceil($weather['temp']/10)),($weather['temp']/10)-floor($weather['temp']/10));?>"><?=$weather['temp'];?></div><?php } ?>
    <div var="day_of_week"><?=date("D");?></div>
    <div class="slideshow" var="date"><?=date("M j, Y");?></div>
    <div class="slideshow" var="sunrise" unit=""><?=Times24ToTime12Short(Settings::LoadSettingsVar("sunrise_txt","6:00"))?></div>
    <div class="slideshow" var="sunset" unit=""><?=Times24ToTime12Short(Settings::LoadSettingsVar("sunset_txt","6:00"))?></div>
    <?php if(HasPlugin("NullWeather")) { ?><div class="temp extra NullWeather" var="feels_like" unit="fahrenheit"><?=$weather['feels_like'];?></div>
    <div class="humidity extra NullWeather" var="humidity" unit="percent"><?=$weather['humidity'];?></div>
    <div class="wind extra NullWeather" var="wind_speed" unit="MetersPerSeconds"><?=$weather['wind_speed'];?></div>
    <div class="description extra NullWeather" var="description"><?=$weather['description'];?></div>
    <div class="temp_chart outdoors simple NullWeather">
        <div class="time_bar"></div>
        <div class="hour" hour="00"></div>
        <div class="hour" hour="01"></div>
        <div class="hour" hour="02"></div>
        <div class="hour" hour="03"></div>
        <div class="hour" hour="04"></div>
        <div class="hour" hour="05"></div>
        <div class="hour" hour="06"></div>
        <div class="hour" hour="07"></div>
        <div class="hour" hour="08"></div>
        <div class="hour" hour="09"></div>
        <div class="hour" hour="10"></div>
        <div class="hour" hour="11"></div>
        <div class="hour" hour="12"></div>
        <div class="hour" hour="13"></div>
        <div class="hour" hour="14"></div>
        <div class="hour" hour="15"></div>
        <div class="hour" hour="16"></div>
        <div class="hour" hour="17"></div>
        <div class="hour" hour="18"></div>
        <div class="hour" hour="19"></div>
        <div class="hour" hour="20"></div>
        <div class="hour" hour="21"></div>
        <div class="hour" hour="22"></div>
        <div class="hour" hour="23"></div>
    </div><?php } ?>
    <?php if(HasPlugin("NullSensors")) { ?><div class="temp_chart indoors simple NullSensors" collection="indoor_temperature" room_id="all">
        <div class="time_bar"></div>
        <div class="hour" hour="00"></div>
        <div class="hour" hour="01"></div>
        <div class="hour" hour="02"></div>
        <div class="hour" hour="03"></div>
        <div class="hour" hour="04"></div>
        <div class="hour" hour="05"></div>
        <div class="hour" hour="06"></div>
        <div class="hour" hour="07"></div>
        <div class="hour" hour="08"></div>
        <div class="hour" hour="09"></div>
        <div class="hour" hour="10"></div>
        <div class="hour" hour="11"></div>
        <div class="hour" hour="12"></div>
        <div class="hour" hour="13"></div>
        <div class="hour" hour="14"></div>
        <div class="hour" hour="15"></div>
        <div class="hour" hour="16"></div>
        <div class="hour" hour="17"></div>
        <div class="hour" hour="18"></div>
        <div class="hour" hour="19"></div>
        <div class="hour" hour="20"></div>
        <div class="hour" hour="21"></div>
        <div class="hour" hour="22"></div>
        <div class="hour" hour="23"></div>
    </div>
    <div class="temp indoors NullSensors" var="temp" unit="fahrenheit"></div>
    <div class="humidity indoors NullSensors" var="hum" unit="percent"></div><?php } ?>
</div>
<?php
require_once("../../includes/main.php");
$default_name = "null device";
$default_type = ServerType();
$default_dev = $hash =  GitHash($root_path);
if($hash != "dev") $default_dev = "production";
if(isset($device_info)){
	$default_name = $device_info['name'];
	$default_type = $device_info['type'];
}
$main = Servers::GetMain();
$hubs = Servers::GetAllHubs();
$servers = array_merge(Servers::OnlineServers(),Servers::OfflineServers());
$rooms = Rooms::ActiveRooms();
$room_id = (int)Settings::LoadSettingsVar('room_id',0);
$room_name = "none";
if($room_id){
    $room = Rooms::RoomId($room_id);
    if(!is_null($room)) $room_name = $room['name'];
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<dialog class="side menu" id="main_menu">
    <h1 class="null" type="<?=Settings::LoadSettingsVar('type',$default_type);?>"><?=Settings::LoadSettingsVar('name',$default_name);?> <i><?=Settings::LoadSettingsVar('server',Hostname());?></i></h1>
    <nav>
        <?php if(!Servers::IsMain()) { ?><a href="http://<?=$main['url'];?>/" target="_blank">hub</a><?php } ?>
        <?php if(UserSession::UserLevelCheck(5)) { ?>
            <a href="/api/" target="_blank">api</a>
            <a href="/tests/" target="_blank">tests</a>
        <?php } ?>
    </nav>
    <?php if(HasPlugin("NullProfiles")) { ?>
        <nav class="check-ins">
            <a href="#home_today" model="DailyScheduleOverrides" schedule="day off" date="<?=date("Y-m-d");?>">Check-in</a>
            <a href="#home_tomorrow" model="DailyScheduleOverrides" schedule="day off" date="<?=date("Y-m-d",time()+DaysToSeconds(1));?>">Tomorrow</a>
        </nav>
    <?php } ?>
    <ul id="device_settings" model="settings">
        <li>
            <span class="key">name</span>
            <span class="value field" var="name"><?=Settings::LoadSettingsVar('name',$default_name);?></span>
            <input class="edit field" var="name" type="text" value="<?=Settings::LoadSettingsVar('name',$default_name);?>" />
        </li>
        <li>
            <span class="key">type</span>
            <span class="value field" var="type"><?=Settings::LoadSettingsVar('type',$default_type);?></span>
            <input class="edit field" var="type" type="text" list="device_types" value="<?=Settings::LoadSettingsVar('type',$default_type);?>" />
            <datalist id="device_types">
                <option value="hub">hub</option>
                <option value="thermometer">thermometer</option>
                <option value="micro display">micro display</option>
                <option value="eInk display">eInk display</option>
                <option value="display">display</option>
                <option value="kiosk">kiosk</option>
            </datalist>
        </li>
        <li>
            <span class="key">room</span>
            <span class="value select" var="room_id"><?=$room_name;?></span>
            <select class="edit" var="room_id" model="settings">
                <option value="0"<?php if($room_id == 0) echo " selected"; ?>>none</option>
                <?php foreach($rooms as $room) { ?>
                    <option value="<?=$room['id'];?>"<?php if($room_id == (int)$room['id']) echo " selected"; ?>><?=$room['name'];?></option>
                <?php } ?>
            </select>
        </li>
        <li>
            <span class="key">main hub</span>
            <span class="value select" var="main_hub"><?=$main['name'];?></span>
            <select class="edit" var="main_hub" collection="hub_candidates">
                <option value="call_election">call election</option>
                <optgroup label="hub candidates"><?php foreach($hubs as $hub) { ?>
                    <option value="<?=$hub['mac_address'];?>"<?php if($hub['mac_address'] == $main['mac_address']) echo " selected";?>><?=$hub['name'];?></option>
                <?php } ?></optgroup>
            </select>
        </li>
        <li>
            <span class="key">enabled</span>
            <span class="value bool" var="enabled" val="<?=Settings::LoadSettingsVar('enabled');?>"></span>
        </li>
        <li>
            <span class="key">crawl network</span>
            <span class="value select" var="do_crawl_network"><?=Settings::LoadSettingsVar('do_crawl_network','auto');?></span>
            <select class="edit" var="do_crawl_network" model="settings">
                <option value="auto"<?php if(Settings::LoadSettingsVar('do_crawl_network','auto') == 'auto') echo " selected";?>>auto</option>
                <option value="yes"<?php if(Settings::LoadSettingsVar('do_crawl_network','auto') == 'yes') echo " selected";?>>yes</option>
                <option value="no"<?php if(Settings::LoadSettingsVar('do_crawl_network','auto') == 'no') echo " selected";?>>no</option>
            </select>
        </li>
    </ul>
    <?php if(HasPlugin("NullWeather")) {?>
    <ul id="weather_settings">
        <li>
            <span class="key">api key</span>
            <span class="value field" var="weather_api_key"><?php if(Settings::LoadSettingsVar('weather_api_key')) echo "{show key}"; else echo "{add key}" ;?></span>
            <input class="edit field" var="weather_api_key" type="text" value="<?=Settings::LoadSettingsVar('weather_api_key');?>" />
        </li>
        <li>
            <span class="key">city</span>
            <span class="value field" var="weather_city"><?=Settings::LoadSettingsVar('weather_city');?></span>
            <input class="edit field" var="weather_city" type="text" value="<?=Settings::LoadSettingsVar('weather_city');?>" />
        </li>
        <li>
            <span class="key">log days</span>
            <span class="value field" var="weather_log_days" unit="days"><?=Settings::LoadSettingsVar('weather_log_days');?></span>
            <input class="edit field" var="weather_log_days" type="number" min="1" step="1" max="15" value="<?=Settings::LoadSettingsVar('weather_log_days');?>" />
        </li>
        <li>
            <span class="key">units</span>
            <span class="value select" var="weather_units"><?=Settings::LoadSettingsVar('weather_units','imperial');?></span>
            <select class="edit" var="weather_units">
                <option value="imperial"<?php if(Settings::LoadSettingsVar('weather_units','imperial') == 'imperial') echo " selected";?>>imperial</option>
                <option value="metric"<?php if(Settings::LoadSettingsVar('weather_units','imperial') == 'metric') echo " selected";?>>metric</option>
            </select>
        </li>
        <li>
            <span class="key">one call</span>
            <span class="value bool" var="weather_one_call" val="<?=Settings::LoadSettingsVar('weather_one_call');?>"></span>
        </li>
    </ul>
    <?php } ?>
    <?php if(HasPlugin("NullLights")) { ?>
    <ul id="light_settings">
        <li>
            <span class="key">observe</span>
            <span class="value select" var="do_wemo_observe"><?=Settings::LoadSettingsVar('do_wemo_observe','auto');?></span>
            <select class="edit" var="do_wemo_observe" model="settings">
                <option value="auto"<?php if(Settings::LoadSettingsVar('do_wemo_observe','auto') == 'auto') echo " selected";?>>auto</option>
                <option value="yes"<?php if(Settings::LoadSettingsVar('do_wemo_observe','auto') == 'yes') echo " selected";?>>yes</option>
                <option value="no"<?php if(Settings::LoadSettingsVar('do_wemo_observe','auto') == 'no') echo " selected";?>>no</option>
            </select>
        </li>
        <li>
            <span class="key">automation</span>
            <span class="value select" var="light_automation_mode"><?=Settings::LoadSettingsVar('light_automation_mode','main');?></span>
            <select class="edit" var="light_automation_mode" model="settings">
                <option value="main"<?php if(Settings::LoadSettingsVar('light_automation_mode','main') == 'main') echo " selected";?>>main</option>
                <option value="practice"<?php if(Settings::LoadSettingsVar('light_automation_mode','main') == 'practice') echo " selected";?>>practice</option>
                <option value="off"<?php if(Settings::LoadSettingsVar('light_automation_mode','main') == 'off') echo " selected";?>>off</option>
            </select>
        </li>
        <li>
            <span class="key">log days</span>
            <span class="value field" var="wemo_log_days" unit="days"><?=Settings::LoadSettingsVar('wemo_log_days');?></span>
            <input class="edit field" var="wemo_log_days" type="number" min="1" step="1" max="5" value="<?=Settings::LoadSettingsVar('wemo_log_days');?>" />
        </li>
        <li>
            <span class="key" title="how many days of archive to include in pixel charts">archive chart</span>
            <span class="value field" var="wemo_archive_chart_days" unit="days"><?=Settings::LoadSettingsVar('wemo_archive_chart_days');?></span>
            <input class="edit field" var="wemo_archive_chart_days" type="number" min="1" step="1" max="7" value="<?=Settings::LoadSettingsVar('wemo_archive_chart_days');?>" />
        </li>
    </ul>
    <?php } ?>
    <ul id="servers">
        <?php $i = 0; foreach($servers as $server) { ?>
            <li server_id="<?=$server['mac_address'];?>" class="server" index="<?=$i++;?>" online="<?=$server['online'];?>" type="<?=$server['type'];?>">
                <a href="http://<?=$server['url'];?>/" target="_blank" var="name"><?=$server['name'];?></a>
            </li>
        <?php } ?>
    </ul>
    <nav>
        <?php if(UserSession::UserLevelCheck(5)) { ?>
        <?php if(hasPhpMyAdmin()) { ?><a href="/phpMyAdmin/" target="_blank">phpMyAdmin</a><?php } ?>
        <a href="/helpers/" target="_blank">helpers</a>
        <?php } ?>
    </nav>
</dialog>
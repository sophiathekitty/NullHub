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
$hub = Servers::GetHub();
$data = [
	'info' => [
		'url' => LocalIp(),
		'is_hub' => Servers::IsHub(),
		'hub' => $hub['url'],
		'hub_name' => $hub['name'],
		'room' => Settings::LoadSettingsVar('room_id',0),
		'type' => Settings::LoadSettingsVar('type',$default_type),
		'enabled' => Settings::LoadSettingsVar('enabled',1),
		'main' => Settings::LoadSettingsVar('main',0),
		'dev' => Settings::LoadSettingsVar('dev',$default_dev),
		'hash' => $hash,
		'modified' => FolderHashDate($root_path.Settings::LoadSettingsVar('path',"/")),
		'path' => Settings::LoadSettingsVar('path',"/"),
		'server' => Hostname(),//$settings->LoadVar('server',Hostname()),
		'mac_address' => LocalMac(),
		'name' => Settings::LoadSettingsVar('name',$default_name),
		'git' => gitHubUrl(),
		'setup' => SetupState()
		]
	];
OutputJson($data);	

?>

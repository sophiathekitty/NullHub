<?php
require_once("../../includes/main.php");
$settings = new Settings();
$hub = Servers::GetHub();
$data = [
	'info' => [
		'url' => LocalIp(),
		'is_hub' => Servers::IsHub(),
		'hub' => $hub['url'],
		'hub_name' => $hub['name'],
		'room' => $settings->LoadVar('room_id',0),
		'type' => $settings->LoadVar('type',"device"),
		'enabled' => $settings->LoadVar('enabled',1),
		'main' => $settings->LoadVar('main',0),
		'dev' => $settings->LoadVar('dev',"production"),
		'hash' => FolderHash($root_path.$settings->LoadVar('path',"/")),
		'modified' => FolderHashDate($root_path.$settings->LoadVar('path',"/")),
		'path' => $settings->LoadVar('path',"/"),
		'server' => $settings->LoadVar('server',"pi"),
		'mac_address' => Settings::LoadSettingsVar("mac_address",LocalMac()),
		'name' => $settings->LoadVar('name',"null device"),
		'git' => gitHubUrl()
		]
	];
OutputJson($data);
?>

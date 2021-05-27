<?php
require_once("../../../includes/main.php");
$settings = new Settings();

$data = [
	'info' => [
		'url' => $_SERVER['HTTP_HOST'],
		'path' => "http://".$_SERVER['HTTP_HOST'].$settings->LoadVar('path',"/"),
		'app_path' => "http://".$_SERVER['HTTP_HOST'].$settings->LoadVar('path')."app",
		'type' => $settings->LoadVar('type',"device"),
		'main' => $settings->LoadVar('main',"0"),
		'server' => $settings->LoadVar('server',"pi"),
		'room' => $settings->LoadVar('room_id',"0"),
		'enabled' => $settings->LoadVar('enabled',"1"),
		'mac_address' => LocalMacAddress(),
		'name' => $settings->LoadVar('name',"null device")
		]
	];
$data['apis'] = LocalPluginApis(LocalAPIs());
$data['extensions'] = FindLocalExtensions();
$data['plugins'] = FindPluginsName($root_path."plugins/");
OutputJson($data);
?>
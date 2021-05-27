<?php
require_once("../../includes/main.php");
$settings = new Settings();

$data = [
	'info' => [
		'url' => $_SERVER['HTTP_HOST'],
		'room' => $settings->LoadVar('room_id',0),
		'type' => $settings->LoadVar('type',"device"),
		'enabled' => $settings->LoadVar('enabled',1),
		'main' => $settings->LoadVar('main',0),
		'path' => $settings->LoadVar('path',"/"),
		'server' => $settings->LoadVar('server',"pi"),
		'mac_address' => LocalMacAddress(),
		'name' => $settings->LoadVar('name',"null device")
		]
	];
OutputJson($data);
?>

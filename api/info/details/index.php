<?php
require_once("../../../includes/main.php");
$settings = new Settings();
$hub = Servers::GetHub();

$data = [
	'info' => [
		'url' => LocalIp(),
		'path' => "http://".LocalIp().$settings->LoadVar('path',"/"),
		'app_path' => "http://".LocalIp().$settings->LoadVar('path')."app",
		'type' => $settings->LoadVar('type',"device"),
		'main' => $settings->LoadVar('main',"0"),
		'dev' => $settings->LoadVar('dev',"production"),
		'hash' => FolderHash($root_path.$settings->LoadVar('path',"/")),
		'modified' => FolderHashDate($root_path.$settings->LoadVar('path',"/")),
		'includes' => FolderModifiedDate($root_path.$settings->LoadVar('path',"/")."includes/"),
		'models' => FolderModifiedDate($root_path.$settings->LoadVar('path',"/")."models/"),
		'modules' => FolderModifiedDate($root_path.$settings->LoadVar('path',"/")."modules/"),
		'api' => FolderModifiedDate($root_path.$settings->LoadVar('path',"/")."api/"),
		'app' => FolderModifiedDate($root_path.$settings->LoadVar('path',"/")."app/"),
		'server' => $settings->LoadVar('server',"pi"),
		'room' => $settings->LoadVar('room_id',"0"),
		'enabled' => $settings->LoadVar('enabled',"1"),
		'is_hub' => Servers::IsHub(),
		'hub' => "http://".$hub['url']."/",
		'hub_name' => $hub['name'],
		'mac_address' => Settings::LoadSettingsVar("mac_address",LocalMac()),
		'name' => $settings->LoadVar('name',"null device"),
		'git' => gitHubUrl()
		]
	];
$data['apis'] = LocalPluginApis(LocalAPIs());
$data['extensions'] = LocalExtensions(true);//FindLocalExtensions();
$data['plugins'] = LocalPluginInfo(true);//FindPluginsName($root_path."plugins/");
OutputJson($data);
?>
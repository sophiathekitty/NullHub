<?php
require_once("../../../includes/main.php");
$data = [];
//$data['post'] = $_POST;
//$data['get'] = $_GET;
//$data['request'] = $_REQUEST;
if (isset($_GET['database'],$_GET['username'],$_GET['password'])){
	CreateSettingsFile($_GET);
	$data['install'] = SetupInstallDatabase();
}
if(defined("SETUP_MODE")){
	$data['defaults'] = [];
	$data['defaults']["device_type"] = ServerType();	
}
if(SetupComplete()) $data['setup'] = "complete";
OutputJson($data);
?>
<?php
require_once("../../../includes/main.php");
$servers = new Servers();
$data = [];
if (isset($_GET['name'],$_GET['type'],$_GET['mac_address'],$_GET['url'])){
	$server = $servers->LoadByMacAddress($_GET['mac_address']);
	$main = 0;
	if(isset($_GET['main'])) $main = $_GET['main'];
	if(is_null($server)){
		$data['added'] = $servers->Add($_GET['name'],$_GET['type'],$_GET['mac_address'],$_GET['url'],$main);
	} else {
		$data['updated'] = $servers->Update($_GET['name'],$_GET['type'],$_GET['mac_address'],$_GET['url'],$main);
	}
}
// return servers
if(isset($_GET['hub'])){
	$data['hub'] = $servers->Hub();
} elseif(isset($_GET['online'])){
	$data['servers'] = $servers->Online();
} elseif(isset($_GET['offline'])){
	$data['servers'] = $servers->Offline();
} else {
	$data['servers'] = $servers->LoadAll();
}
OutputJson($data);
?>
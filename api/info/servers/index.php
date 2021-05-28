<?php
require_once("../../../includes/main.php");
$data = [];
if (isset($_GET['name'],$_GET['type'],$_GET['mac_address'],$_GET['url'])){
	$data['save'] = Servers::SaveServer($_GET);
}
// return servers
if(isset($_GET['hub'])){
	$data['hub'] = Servers::GetHub();
} else {
	$data['servers'] = Servers::OnlineServers();
}
OutputJson($data);
?>
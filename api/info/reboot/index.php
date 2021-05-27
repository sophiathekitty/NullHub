<?php
require_once("../../../includes/main.php");
$settings = new Settings();
$data = [];
if(isset($_GET['reboot'])){
	if($_GET['reboot']){
		$settings->SaveVar('reboot_requested',1);
	} else {
		$settings->SaveVar('reboot_requested',0);
	}
}
$data['reboot'] = LoadSettingVar('reboot_requested');
OutputJson($data);
?>
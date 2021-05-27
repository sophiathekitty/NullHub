<?php
require_once("../../../includes/main.php");
$settings = new Settings();
$data = [];
if(isset($_GET['shutdown'])){
	if($_GET['shutdown']){
			$settings->SaveVar('shutdown_requested',1);
	} else {
		$settings->SaveVar('shutdown_requested',0);
	}
}
$data['shutdown'] = $settings->LoadVar('shutdown_requested');
OutputJson($data);
?>
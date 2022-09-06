<?php
require_once("../../../includes/main.php");
$data = [];
if (isset($_GET['database'],$_GET['username'],$_GET['password'])){
	CreateSettingsFile($_GET);
}
if(SetupComplete()) $data['setup'] = "complete";
OutputJson($data);
?>
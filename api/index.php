<?php
require_once("../includes/main.php");
$settings = new Settings();

$data['apis'] = LocalPluginApis(LocalAPIs());
OutputJson($data);
?>
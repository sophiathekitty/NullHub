<?php
require_once("../includes/main.php");
$settings = new Settings();

$data['apis'] = LocalExtensionApis(LocalPluginApis(LocalAPIs()));
OutputJson($data);
?>
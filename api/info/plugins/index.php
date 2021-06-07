<?php
require_once("../../../includes/main.php");
$data = [];
$data['plugins'] = LocalPluginInfo();//FindPluginsName($root_path."plugins/");
OutputJson($data);
?>
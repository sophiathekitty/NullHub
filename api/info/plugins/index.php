<?php
require_once("../../../includes/main.php");
$data = [];
$verbose = false;
if(isset($_GET['verbose'])) $verbose = true;
$data['plugins'] = LocalPluginInfo($verbose);//FindPluginsName($root_path."plugins/");
OutputJson($data);
?>
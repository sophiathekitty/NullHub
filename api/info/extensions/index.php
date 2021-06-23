<?php
require_once("../../../includes/main.php");
$data = [];
$verbose = false;
if(isset($_GET['verbose'])) $verbose = true;
$data['extensions'] = LocalExtensions($verbose);//FindPluginsName($root_path."plugins/");
OutputJson($data);
?>
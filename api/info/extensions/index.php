<?php
require_once("../../../includes/main.php");
$data = [];
$data['extensions'] = LocalExtensions();//FindPluginsName($root_path."plugins/");
OutputJson($data);
?>
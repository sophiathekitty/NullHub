<?php
require_once("../../includes/main.php");
$settings = new Settings();

$data = [];
$data['sections'] = SlideSections::GetSections();
OutputJson($data);
?>

<?php
require_once("../../../includes/main.php");
$data = [];

$data['pallet'] = FullColorPallet();

OutputJson($data);
?>
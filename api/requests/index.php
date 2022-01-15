<?php
require_once("../../includes/main.php");
$data = [];
$data['servers'] = RequestReport();
OutputJson($data);
?>

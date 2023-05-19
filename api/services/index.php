<?php
require_once("../../includes/main.php");
$data = [];
$data['services'] = ServicesLogs::ServicesList();
OutputJson($data);
?>
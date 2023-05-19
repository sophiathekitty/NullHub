<?php
require_once("../../../includes/main.php");
$data = [];
if(isset($_GET['name'])){
    $data['service_logs'] = ServicesLogs::ServiceLogs($_GET['name']);
} else {
    $data['error'] = "missing service name";
}
OutputJson($data);
?>
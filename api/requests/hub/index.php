<?php
require_once("../../../includes/main.php");
$data = ['error'=>'missing api path'];
if(isset($_GET['api'])){
    $data = ServerRequests::LoadHubJSON($_GET['api']);
}
OutputJson($data);
?>

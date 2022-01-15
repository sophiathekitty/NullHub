<?php
require_once("../../../includes/main.php");
$data = [];
if(isset($_GET['api'])){
    $data = ServerRequests::LoadHubJSON($_GET['api']);
}
OutputJson($data);
?>

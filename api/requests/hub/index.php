<?php
require_once("../../../includes/main.php");
$data = [];
if(isset($_GET['api']) && UserSession::UserIsServer()){
    $data = ServerRequests::LoadHubJSON($_GET['api']);
}
OutputJson($data);
?>

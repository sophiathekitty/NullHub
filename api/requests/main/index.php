<?php
require_once("../../../includes/main.php");
$data = [];
if(isset($_GET['api'])){
    $data = ServerRequests::LoadMainJSON($_GET['api']);
}
OutputJson($data);
?>

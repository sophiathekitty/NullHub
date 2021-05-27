<?php
require_once("../../includes/main.php");
$data = ['session'=>UserSession::CleanSessionData()];
OutputJson($data);
?>
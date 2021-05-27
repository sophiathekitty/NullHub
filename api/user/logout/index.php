<?php
require_once("../../../includes/main.php");
$session = new UserSession();
$session->LogoutUserSession();
$session->ClearToken();
$data = ['session'=>UserSession::CleanSessionData(UserSession::$session)];
OutputJson($data);
?>
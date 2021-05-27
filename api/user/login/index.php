<?php
require_once("../../../includes/main.php");
$session = new UserSession();
if(isset($_GET['username'],$_GET['password'])){
    $session = $session->LoginUserSession($_GET['username'],$_GET['password']);
}
$data = ['session'=>UserSession::CleanSessionData(UserSession::$session)];
OutputJson($data);
?>
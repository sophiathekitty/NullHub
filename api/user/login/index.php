<?php
require_once("../../../includes/main.php");
$session = new UserSession();
if(isset($_GET['username'],$_GET['password'])){
    $session = $session->LoginUserSession($_GET['username'],$_GET['password']);
}
$data = [];
if(isset($_GET['ip']) && UserSession::UserIsServer()){
    $user_logins = new UserLogins();
    $data['user_login'] = $user_logins->LoadByIp($_GET['ip']);
}
$data['session'] = UserSession::CleanSessionData(UserSession::$session);
OutputJson($data);
?>
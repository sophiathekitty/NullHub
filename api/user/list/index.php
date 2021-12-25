<?php
require_once("../../../includes/main.php");
$session = new UserSession();
$data = [];

if(UserSession::UserIsServer()){
    $data['users'] = Users::AllUsers();
}
OutputJson($data);
?>
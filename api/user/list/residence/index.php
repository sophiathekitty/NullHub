<?php
require_once("../../../../includes/main.php");
$session = new UserSession();
$data = [];

if(UserSession::UserLevelCheck(3)){
    $data['users'] = Users::Residence();
    for($i = 0; $i < count($data['users']); $i++){
        $data['users'][$i]['password'] = "[redacted]";
    }
}
OutputJson($data);
?>
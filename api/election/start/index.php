<?php
require_once("../../../includes/main.php");
$stage = Settings::LoadSettingsVar("election_stage");
$data = [];
if(isset($_GET['election_manager'])){
    $res = Settings::SaveSettingsVar("election_manager",$_GET['election_manager']);
    $data['election_manager'] = $res['row'];
    $res = Settings::SaveSettingsVar("election_stage","start");
    $data['election_stage'] = $res['row'];
    $res = Settings::SaveSettingsVar("election_started",date("Y-m-d"));
    $data['election_started'] = $res['row'];
} else {
    $data['missing'] = "send the mac address of the election manager to start polling";
}
OutputJson($data);
?>
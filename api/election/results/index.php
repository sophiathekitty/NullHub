<?php
require_once("../../includes/main.php");
$stage = Settings::LoadSettingsVar("election_stage");
$data = [];
if(isset($_GET['election_results'])){
    $data = Elections::ElectionResults($_GET['election_results']);
} else {
    $data['missing'] = "send the mac address of the new main hub";
}
OutputJson($data);
?>
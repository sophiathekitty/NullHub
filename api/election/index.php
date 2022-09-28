<?php
require_once("../../includes/main.php");
$stage = Settings::LoadSettingsVar("election_stage");
$data = [
    'stage'=>$stage,
    'manager'=>Settings::LoadSettingsVar("election_manager"),
    'started'=>Settings::LoadSettingsVar("election_started")
];
$data['vote'] = HubCandidates::AllCandidates();
OutputJson($data);
?>
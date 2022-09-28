<?php
require_once("../../includes/main.php");
$stage = Settings::LoadSettingsVar("election_stage");
$data = [
    'stage'=>$stage,
    'manager'=>Settings::LoadSettingsVar("election_manager"),
    'started'=>Settings::LoadSettingsVar("election_started")
];
if($stage == "polling") $data['poll'] = HubCandidates::AllCandidates();
if($stage == "done") $data['vote'] = HubCandidates::TopCandidate();
OutputJson($data);
?>
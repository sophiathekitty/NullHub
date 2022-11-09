<?php
require_once("../../../includes/main.php");
$data = [];
if(isset($_GET['mac_address'])){
    if($_GET['mac_address'] == 'call_election'){
        /*
        Elections::CallElection();
        Elections::RunElection();
        $data['stage'] =   Settings::LoadSettingsVar("election_stage");
        $data['manager'] = Settings::LoadSettingsVar("election_manager");
        $data['started'] = Settings::LoadSettingsVar("election_started");
        */
        $data['practice'] = "call election";
    } else {
        /*
        $data = Elections::ElectionResults($_GET['mac_address']);
        $servers = Servers::OnlineServers();
        foreach($servers as $server){
            if($server['mac_address'] != LocalMac() && $server['type'] != "grow_manager"){
                ServerRequests::LoadRemoteJSON($server['mac_address'],"/api/election/results/?election_results=".$_GET['mac_address']);
            }
        }
        */
        $data['practice'] = "appoint main hub";
    }
} else {
    $data['missing'] = "send the mac address of the new main hub";
}
OutputJson($data);
?>
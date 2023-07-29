<?php
require_once("../../../includes/main.php");
$data = [];
if(isset($_GET['mac_address'])){
    if($_GET['mac_address'] == 'call_election'){
        Elections::CallElection();
        Elections::RunElection();
        $data['stage'] =   Settings::LoadSettingsVar("election_stage");
        $data['manager'] = Settings::LoadSettingsVar("election_manager");
        $data['started'] = Settings::LoadSettingsVar("election_started");
    } else {
        $data = Elections::ElectionResults($_GET['mac_address']);
        $servers = Servers::OnlineServers();
        $data['servers'] = [];
        foreach($servers as $server){
            if($server['mac_address'] != LocalMac() && $server['type'] != "grow_manager"){
                $res = ServerRequests::LoadRemoteJSON($server['mac_address'],"/api/election/results/?election_results=".$_GET['mac_address']);
                $data['servers'][] = ['server'=>$server,'results'=>$res];
            }
        }
    }
} else {
    $data['missing'] = "send the mac address of the new main hub";
}
OutputJson($data);
?>
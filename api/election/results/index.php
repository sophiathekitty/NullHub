<?php
require_once("../../includes/main.php");
$stage = Settings::LoadSettingsVar("election_stage");
$data = [];
if(isset($_GET['election_results'])){
    $main = Servers::GetMain();
    if($main['mac_address'] == $_GET['election_results']){
        $data['main'] = "no change";
    } else {
        $data['main'] = "changed";
        $main['main'] = 0;
        $res = Servers::SaveServer($main);
        $data['old_main'] = $res['row'];
        $server = Servers::ServerMacAddress($_GET['election_results']);
        $server['main'] = 1;
        $res = Servers::SaveServer($main);
        $data['new_main'] = $res['row'];

        $res = Settings::SaveSettingsVar("election_stage","done");
    }
    
} else {
    $data['missing'] = "send the mac address of the new main hub";
}
OutputJson($data);
?>
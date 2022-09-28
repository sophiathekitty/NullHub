<?php
/**
 * a module for doing hub elections
 */
class Elections {
    private static $stage;
    /**
     * run an election
     */
    public static function RunElection(){
        Elections::$stage = Settings::LoadSettingsVar("election_stage","done");
        if(Elections::$stage == "done" && Elections::MainOK()) return; // return if election is done and main is ok
        Services::Start("NullHub::Election");
        if(Elections::$stage == "start") Elections::StartElection();
        if(Elections::$stage == "polling") Elections::RunPoll();
        Services::Complete("NullHub::Election");
    }
    /**
     * do we need to run an election?
     */
    public static function MainOK(){
        if(!Servers::IsHub()) return true;
        $main = Servers::GetMain();
        if($main['offline'] > 10) return Elections::CallElection();
        return true;
    }
    /**
     * do we need to run an election? lets make sure everybody agrees on who is the hub
     */
    public static function DoesAgreeOnWhoIsHub(){
        // check
    }
    /**
     * return true if this device is the election manager
     */
    public static function IsElectionManager(){
        return (Settings::LoadSettingsVar("election_manager") == LocalMac());
    }
    /**
     * do we need to run an election?
     */
    public static function CallElection(){
        Settings::SaveSettingsVar("election_stage","start");
        Settings::SaveSettingsVar("election_manager",LocalMac());
        Settings::SaveSettingsVar("election_started",date("Y-m-d"));
        return false;
    }
    /**
     * start an election
     */
    public static function StartElection(){
        Services::Log("NullHub::Election","StartElection");
        HubCandidates::ClearCandidates();
        Elections::FindCandidates();
        if(Elections::IsElectionManager()) Elections::AnnounceElection();
    }

    public static function AnnounceElection(){
        Services::Log("NullHub::Election","AnnounceElection");
        $servers = Servers::OnlineServers();
        foreach($servers as $server){
            if($server['type'] != 'grow_manager'){
                $res = ServerRequests::LoadRemoteJSON($server['mac_address'],"/api/election/start/?election_manager=".LocalMac());
                Services::Log("NullHub::Election","AnnounceElection ".$server['name']);
                Debug::Log($res);
            }
        }
    }
    public static function FindCandidates(){
        Services::Log("NullHub::Election","FindCandidates");
        $servers = Servers::OnlineServers();
        foreach($servers as $server){
            if($server['type'] == "hub" || $server['type'] == "kiosk" || $server['type'] == "old_hub"){
                Services::Log("NullHub::Election",$server['name']." [".$server['type']."]");
                HubCandidates::SaveCandidates($server);
            }
        }
        Settings::SaveSettingsVar("election_stage","polling");
    }
    public static function RunPoll(){
        Services::Log("NullHub::Election","RunPoll");
        $candidates = HubCandidates::AllCandidates();
        if(count($candidates) == 0) Elections::FindCandidates();
        foreach($candidates as $candidate){
            $info = ServerRequests::LoadRemoteJSON($candidate['mac_address'],"/api/info");
            $plugins = ServerRequests::LoadRemoteJSON($candidate['mac_address'],"/api/info/plugins");
            $extension = ServerRequests::LoadRemoteJSON($candidate['mac_address'],"/api/info/extensions");
            if(is_array($plugins) && isset($plugins['plugins'])) $candidate['plugins'] = count($plugins['plugins']);
            if(is_array($extension) && isset($extension['extensions'])) $candidate['extensions'] = count($extension['extensions']);
            $candidate['latency'] = ServerLatency($extension['mac_address']);
            $candidate['score'] = 0;
            $server = Servers::ServerMacAddress($candidate['mac_address']);
            if($server['type'] == "hub") $candidate['score'] += 100_000;
            if($server['type'] == "hub_old") $candidate['score'] += 150_000;
            if($server['type'] == "kiosk") $candidate['score'] += 50_000;
            if(isset($info['info']['dev']) && $info['info']['dev'] == "production") $candidate['score'] += 100_000;
            $candidate['score'] += ($candidate['plugins'] * 10_000);
            $candidate['score'] += ($candidate['extensions'] * 10_000);
            $candidate['score'] -= ($candidate['latency'] * 1_000);
            HubCandidates::SaveCandidates($candidate);
        }
        if(Servers::IsHub())  Elections::RankCandidates();
    }
    public static function RankCandidates(){
        Services::Log("NullHub::Election","RankCandidates");
        $candidates = HubCandidates::AllCandidates();
        $rank = count($candidates);
        foreach($candidates as $candidate){
            $server = Servers::ServerMacAddress($candidate['mac_address']);
            $server['rank'] = $rank;
            $rank--;
            Servers::SaveServer($server);
        }
    }
    public static function CompleteElection(){
        Services::Log("NullHub::Election","CompleteElection");
        Settings::SaveSettingsVar("election_stage","done");

    }
    public static function AnnounceWinner(){

    }
}
?>
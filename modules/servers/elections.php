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
        //if(!Servers::IsHub()) return true;
        if(Servers::IsMain()) return true;
        $main = Servers::GetMain();
        if($main['online'] == 1) return true;
        $hub = Servers::GetMain();
        if($hub['mac_address'] == $main['mac_address']) return true;
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
    /**
     * announce to the other null devices that an election is on
     * TODO: Turn this back on... (commented out for until done testing other stuff)
     */
    public static function AnnounceElection(){
        Services::Log("NullHub::Election","AnnounceElection");
        $servers = Servers::OnlineServers();
        foreach($servers as $server){
            if($server['type'] != 'grow_manager'){
                Services::Log("NullHub::Election","AnnounceElection ".$server['name']);
                Services::Log("NullHub::Election","AnnounceElection (commented out for until done testing other stuff)");
                Services::Log("NullHub::Election","AnnounceElection (TODO:Turn this back on.. modules/servers/elections.php)");
                /*
                $res = ServerRequests::LoadRemoteJSON($server['mac_address'],"/api/election/start/?election_manager=".LocalMac());
                Debug::Log($res);
                */
            }
        }
    }
    /**
     * finds the potential main hub candidates
     */
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
    /**
     * run a poll of the potential hubs. try to load their info and plugins and extensions to get 
     * more info on them. and then calculate a score based on their type (prefer hubs, or old_hub, 
     * but kiosk will do in a pinch) and by how many plugins and extensions they have. (ideally full 
     * coverage of all the plugins and extensions) and then take the latency into account.
     */
    public static function RunPoll(){
        Services::Log("NullHub::Election","RunPoll");
        $candidates = HubCandidates::AllCandidates();
        if(count($candidates) == 0) Elections::FindCandidates();
        foreach($candidates as $candidate){
            
            Services::Log("NullHub::Election","RunPoll::".$candidate['mac_address']);
            $info = ServerRequests::LoadRemoteJSON($candidate['mac_address'],"/api/info");
            Debug::LogGroup("NullHub::Election","RunPoll--info",$info);
            Services::Log("NullHub::Election","RunPoll::info.name:".$info['info']['name']);
            $plugins = ServerRequests::LoadRemoteJSON($candidate['mac_address'],"/api/info/plugins");
            Debug::LogGroup("NullHub::Election","RunPoll--plugins",$plugins);
            $extension = ServerRequests::LoadRemoteJSON($candidate['mac_address'],"/api/info/extensions");
            Debug::LogGroup("NullHub::Election","RunPoll--extensions",$extension);
            if(is_array($plugins) && isset($plugins['plugins'])) $candidate['plugins'] = count($plugins['plugins']);
            if(is_array($extension) && isset($extension['extensions'])) $candidate['extensions'] = count($extension['extensions']);
            $candidate['latency'] = ServerLatency($candidate['mac_address']);
            Services::Log("NullHub::Election","RunPoll::latency:".$candidate['latency'] );
            $candidate['score'] = 0;
            $server = Servers::ServerMacAddress($candidate['mac_address']);
            Services::Log("NullHub::Election","RunPoll::type:".$server['type']);
            Services::Log("NullHub::Election","RunPoll::score:".$candidate['score']);
            if($server['type'] == "hub") $candidate['score'] += 100000;
            Services::Log("NullHub::Election","RunPoll::score:".$candidate['score']);
            if($server['type'] == "old_hub") $candidate['score'] += 175000;
            Services::Log("NullHub::Election","RunPoll::score:".$candidate['score']);
            if($server['type'] == "kiosk") $candidate['score'] += 50000;
            Services::Log("NullHub::Election","RunPoll::score:".$candidate['score']);
            if(isset($info['info']['dev']) && $info['info']['dev'] == "production") $candidate['score'] += 100000;
            Services::Log("NullHub::Election","RunPoll::score:".$candidate['score']);
            $candidate['score'] += ($candidate['plugins'] * 10000);
            Services::Log("NullHub::Election","RunPoll::score:".$candidate['score']);
            $candidate['score'] += ($candidate['extensions'] * 10000);
            Services::Log("NullHub::Election","RunPoll::score:".$candidate['score']);
            $candidate['score'] -= ($candidate['latency'] * 1000);
            Services::Log("NullHub::Election","RunPoll::score:".$candidate['score']);
            $report = HubCandidates::SaveCandidates($candidate);
            Debug::LogGroup("NullHub::Election","HubCandidates::SaveCandidates",$report);
        }
        //if(Servers::IsHub() || ELections::IsElectionManager())
        Elections::RankCandidates();
    }
    /**
     * apply the candidate's scores to their server's rank
     * the top rank is the number of candidates
     */
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
    /**
     * apply the candidate's scores to their server's rank
     * the top rank is the number of candidates
     */
    public static function CountVotes(){
        Services::Log("NullHub::Election","CountVotes");
        $servers = Servers::OnlineServers();
        $candidates = HubCandidates::AllCandidates();
        $count = 1;
        foreach($servers as $server){
            $vote = ServerRequests::LoadRemoteJSON($server['mac_address'],"/api/election/");
            if(is_array($vote) && isset($vote['vote']) && is_array($vote['vote'])){
                foreach($vote['vote'] as $v){
                    foreach($candidates as $candidate){
                        if($candidate['mac_address'] == $v['mac_address']) {
                            $candidate['latency'] += $v['latency'];
                            $candidate['score'] += $v['score'];
                        }
                    }
                }
                $count++;
            }
        }
        foreach($candidates as $candidate){
            $candidate['latency'] /= $count;
            $candidate['score'] /= $count;
            HubCandidates::SaveCandidates($candidate);
        }
    }
    /**
     * Complete Election?
     */
    public static function CompleteElection(){
        if(Settings::LoadSettingsVar("election_stage") == "done") return;
        Services::Log("NullHub::Election","CompleteElection");
        Settings::SaveSettingsVar("election_stage","done");
        Settings::SaveSettingsVar("election_manager","none");
        if(Elections::IsElectionManager()) Elections::AnnounceWinner();
    }
    /**
     * Announce Winner
     */
    public static function AnnounceWinner(){
        Services::Log("NullHub::Election","AnnounceWinner");
        Elections::CountVotes();
    }
}
?>
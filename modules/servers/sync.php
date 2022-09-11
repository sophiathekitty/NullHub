<?php
/**
 * sync servers from hub
 */
function SyncServers(){
    Debug::Log("Sync Servers?");
    if(Servers::IsHub()) return null;
    $servers = ServerRequests::LoadHubJSON("/api/info/servers");
    Debug::Log("Process Servers",$servers);
    foreach($servers['servers'] as $server){
        // skip the wemos from the old hub. those are handled by a plugin now
        if(strtolower($server['type']) != "wemo"){
            //Servers::SaveServer($server);
            Debug::Log(Servers::SaveServer($server));
        }
    }
}
?>
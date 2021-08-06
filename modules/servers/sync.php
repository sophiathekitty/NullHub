<?php
function SyncServers(){
    if(Servers::IsHub()) return null;
    $servers = ServerRequests::LoadHubJSON("/api/info/servers");
    foreach($servers as $server){
        // skip the wemos from the old hub. those are handled by a plugin now
        if(strtolower($server['type']) != "wemo"){
            Servers::SaveServer($server);
        }
    }
}
?>
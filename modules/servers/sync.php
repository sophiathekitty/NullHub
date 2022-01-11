<?php
/**
 * sync servers from hub
 */
function SyncServers(){
    if(Servers::IsHub()) return null;
    $servers = ServerRequests::LoadHubJSON("/api/info/servers");
    //echo "\n\n\nProcess Servers";
    foreach($servers['servers'] as $server){
        // skip the wemos from the old hub. those are handled by a plugin now
        if(strtolower($server['type']) != "wemo"){
            Servers::SaveServer($server);
            //print_r(Servers::SaveServer($server));
        }
    }
}
?>
<?php
/**
 * get the average latency for a server
 * @param string $mac_address the mac address of the server
 * @return float the average latency
 */
function ServerLatency($mac_address){
    $requests = ServerRequests::LoadServerRequests($mac_address);
    Debug::LogGroup("ServerLatency",$mac_address,$requests);
    $total = 0;
    if(count($requests) == 0) return null;
    foreach($requests as $request){
        $total += $request['latency'];
    }
    Debug::LogGroup("ServerLatency",$total);
    return $total/count($requests);
}
/**
 * get latency reports for all of the online servers
 * @return array list of servers with their latency report
 */
function RequestReport(){
    $servers = Servers::OnlineServers();
    for($i = 0; $i < count($servers); $i++){
        $servers[$i]['latency'] = LatencyReport($servers[$i]['mac_address']);
    }
    return $servers;
}
/**
 * latency report for a server. how long it takes for each api and the average latency for all of them
 * @param string $mac_address the mac address of the server
 * @return array the latency report
 */
function LatencyReport($mac_address){
    $requests = ServerRequests::LoadServerRequests($mac_address);
    $report = [];
    foreach($requests as $request){
        if(!isset($report[$request['url']])){
            $report[$request['url']] = ['count'=>1,'latency'=>$request['latency']];
        } else {
            $report[$request['url']]['count']++;
            $report[$request['url']]['latency'] += $request['latency'];
        }
    }
    $r = [];
    $r[] = ['api'=>"all",'latency'=>ServerLatency($mac_address),'count'=>count($requests)];
    foreach($report as $key => $value){
        $r[] = ['api'=>$key,'latency'=>($value['latency']/$value['count']),'count'=>$value['count']];
    }
    return $r;
}
?>
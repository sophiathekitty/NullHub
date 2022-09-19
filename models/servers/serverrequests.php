<?php
/**
 * handle server requests and log the latency of requests
 */
class ServerRequests extends clsModel{
    public $table_name = "ServerRequests";
    public $fields = [
        [
            'Field'=>"guid",
            'Type'=>"varchar(50)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"mac_address",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"url",
            'Type'=>"varchar(200)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"latency",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"online",
            'Type'=>"tinyint(1)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"1",
            'Extra'=>""
        ],[
            'Field'=>"created",
            'Type'=>"datetime",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"current_timestamp()",
            'Extra'=>""
        ]
    ];


    private static $servers = null;
    /**
     * @return ServerRequests|clsModel
     */
    private static function GetInstance(){
        if(is_null(ServerRequests::$servers)) ServerRequests::$servers = new ServerRequests();
        return ServerRequests::$servers;
    }
    /**
     * loads latency report a server
     * @param string $mac_address the mac address of the server
     * @return array latency report
     */
    public static function ServerLatency($mac_address){
        $requests = ServerRequests::LoadServerRequests($mac_address);
        if(count($requests) == 0 ) return null;
        $report = [
            'min'=>999999999999999999,
            'max'=>0,
            'average'=>0
        ];
        foreach($requests as $request){
            if($request['latency'] < $report['min']) $report['min'] = $request['latency'];
            if($request['latency'] > $report['max']) $report['max'] = $request['latency'];
            $report['average'] += $report['latency'];
        }
        $report['average'] = $report['average']/count($requests);
        return $report;
    }
    /**
     * loads all the requests for a server
     * @param string $mac_address the mac address of the server
     * @return array array of server request logs
     */
    public static function LoadServerRequests($mac_address){
        $instance = ServerRequests::GetInstance();
        return $instance->LoadAllWhere(['mac_address'=>$mac_address]);
    }
    /**
     * loads api data from the hub
     * @param string $api the api path "/api/info/"
     * @return array associated array of json data
     */
    public static function LoadHubJSON($api){
        $hub = Servers::GetHub();
        if(is_null($hub)) return null;
        return ServerRequests::LoadRemoteJSON($hub['mac_address'],$api);
    }
    /**
     * loads api data from the main hub
     * @param string $api the api path "/api/info/"
     * @return array associated array of json data
     */
    public static function LoadMainJSON($api){
        $hub = Servers::GetMain();
        if(is_null($hub)) return null;
        return ServerRequests::LoadRemoteJSON($hub['mac_address'],$api);
    }
    /**
     * loads api data from a server by mac_address
     * @param string $mac_address the mac_address of the remote server
     * @param string $api the api path "/api/info/"
     * @return array associated array of json data
     */
    public static function LoadRemoteJSON($mac_address,$api){
        $server = Servers::ServerMacAddress($mac_address);
        $url = "http://".$server['url'].$api;
        $time_before = microtime(true);
        $content=@file_get_contents($url);
        $time_after = microtime(true);
        $latency = $time_after - $time_before;
        $server["last_ping"] = date("Y-m-d H:i:s");
        $server['online'] = 0;
        if(!is_null($content) && $content != "") $server['online'] = 1;
        $requests = ServerRequests::GetInstance();
        $requests->PruneField('created',DaysToSeconds(Settings::LoadSettingsVar('latency_log_days',0.05)));
        $requests->Save([
            "guid"=>md5($mac_address.$server['last_ping'].$api),
            "mac_address"=>$mac_address,
            'url'=>$api,
            "latency"=>$latency,
            "online"=>$server['online']
        ]);
        Servers::SaveServer($server);
        if($server['online'] = 0) return null;
        $json = json_decode($content,true);
        if(is_null($json)) return ['content'=>$content];
        return $json;
    }
    /**
     * loads api data from a server by mac_address
     * @param string $host the ip address
     * @param string $api the api path "/api/info/"
     * @return array associated array of json data
     */
    public static function LoadHostJSON($host,$api){
        $url = "http://".$host.$api;
        if(defined("TEST_MODE") && $host == 'localhost'){
            if(strpos($url,"?") > -1) $url .= "&TEST_MODE=".constant("TEST_MODE");
            else $url .= "?TEST_MODE=".constant("TEST_MODE");
        } 
        if(defined("DEBUG") && $host == 'localhost'){
            if(strpos($url,"?") > -1) $url .= "&DEBUG=".constant("DEBUG");
            else $url .= "?DEBUG=".constant("DEBUG");
        } 
        $content=@file_get_contents($url);
        $json = json_decode($content,true);
        if(is_null($json)) return ['content'=>$content];
        return $json;
    }
    /**
     * loads api data from a server by mac_address
     * @param string $api the api path "/api/info/"
     * @return array associated array of json data
     */
    public static function LoadLocalhostJSON($api){
        return ServerRequests::LoadHostJSON('localhost',$api);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new ServerRequests();
}
?>
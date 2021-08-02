<?php
class ServerRequests extends clsModel{
    public $table_name = "ServerRequests";
    public $fields = [
        [
            'Field'=>"guid",
            'Type'=>"varchar(34)",
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
            'Field'=>"api",
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
    private static function GetInstance(){
        if(is_null(ServerRequests::$servers)) ServerRequests::$servers = new Servers();
        return ServerRequests::$servers;
    }
    public static function LoadHubJSON($api){
        $hub = Servers::GetHub();
        return ServerRequests::LoadRemoteJSON($hub['mac_address'],$api);
    }
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
        $requests->Save(["guid"=>md5($mac_address.$server["last_ping"].$api),"mac_address"=>$mac_address,"api"=>$api,"latency"=>$latency,"online"=>$server['online']]);
        Servers::SaveServer($server);
        if($online = 0) return null;
        return json_decode($content,true);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new ServerRequests();
}
?>
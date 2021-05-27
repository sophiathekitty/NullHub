<?php
class Servers extends clsModel{
    public $table_name = "Servers";
    public $fields = [
        [
            'Field'=>"id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>"auto_increment"
        ],[
            'Field'=>"mac_address",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"name",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"url",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"type",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"main",
            'Type'=>"tinyint(1)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"last_ping",
            'Type'=>"datetime",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"online",
            'Type'=>"tinyint(1)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ]
    ];


    public static $servers = null;
    public static function GetHub(){
        if(is_null(Servers::$servers)) Servers::$servers = new Servers();
        return Servers::$servers->Hub();
    }
    public static function IsHub(){
        $hub = Servers::GetHub();
        return ($hub['mac_address'] == LocalMacAddress());
    }

    public function Hub(){
        return $this->LoadWhere(['main'=>1]);
    }
    public function Online(){
        return $this->LoadAllWhere(['online'=>1]);
    }
    public function Offline(){
        return $this->LoadAllWhere(['online'=>0]);
    }
    public function LoadByUrl($ip){
        if(strpos($ip,"::1") > -1 || $ip == "localhost"){
            return ['id'=>0,'name'=>'localhost','url'=>$ip,'mac_address'=>"localhost"];
        }
        return $this->LoadWhere(['url'=>$ip]);
    }
    public function LoadByMacAddress($mac_address){
        return $this->LoadWhere(['mac_address'=>$mac_address]);
    }
    public function ServerPinged($mac_address){
        return $this->Save(['last_ping'=> date("Y-m-d H:i:s")],['mac_address'=>$mac_address]);
    }
    public function Add($name,$type,$mac_address,$url,$main = 0){
        return $this->Save(['name'=>$name,'type'=>$type,'mac_address'=>$mac_address,'url'=>$url,'main'=>$main,'online'=>1,'last_ping'=>date("Y-m-d H:i:s")]);
    }
    public function Update($name,$type,$mac_address,$url, $main = 0){
        return $this->Save(['name'=>$name,'type'=>$type,'url'=>$url,'main'=>$main,'online'=>1,'last_ping'=>date("Y-m-d H:i:s")],['mac_address'=>$mac_address]);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new Servers();
}

/*
function AllServers(){
    return clsDB::$db_g->select("SELECT * FROM `servers`");
}
function OnlineServers(){
    return clsDB::$db_g->select("SELECT * FROM `servers` WHERE `online` = '1'");
}
function OfflineServers(){
    return clsDB::$db_g->select("SELECT * FROM `servers` WHERE `online` = '0'");
}
function ServerIP($ip){
    if(strpos($ip,"::1") > -1 || $ip == "localhost"){
        return ['id'=>0,'name'=>'localhost','url'=>$ip,'mac_address'=>"localhost"];
    }
    $server = clsDB::$db_g->select("SELECT * FROM `servers` WHERE `url` = '$ip'");
    if(count($server)){
        return $server[0];
    }
    return NULL;
}
function ServerMacAddress($mac_address){
    $server = clsDB::$db_g->select("SELECT * FROM `servers` WHERE `mac_address` = '$mac_address' ORDER BY `last_ping` DESC");
    if(count($server)){
        return $server[0];
    }
    return NULL;
}
function PingServer($mac_address){
    $time = date("Y-m-d H:i:s");
    clsDB::$db_g->safe_update('servers',['last_ping'=>$time],['mac_address'=>$mac_address]);
}
function AddServer($name,$type,$mac_address,$url){
    clsDB::$db_g->safe_insert('servers',array(
        "name" => $name,
        "mac_address" => $mac_address,
        "type" => $type,
        "url" => $url,
        "last_ping" =>  date("Y-m-d H:i:s",time()),
        "online" => 1
    ));
    return ServerMacAddress($mac_address);
}
function Update($name,$type,$mac_address,$url){
    clsDB::$db_g->safe_update('servers',array(
        "name" => $name,
        "type" => $type,
        "url" => $url,
        "last_ping" =>  date("Y-m-d H:i:s",time()),
        "online" => 1
    ), array(
        "mac_address" => $mac_address
    ));
    return ServerMacAddress($mac_address);
}
*/
?>
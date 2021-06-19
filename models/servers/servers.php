<?php
class Servers extends clsModel{
    public $table_name = "Servers";
    public $fields = [
        [
            'Field'=>"mac_address",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"PRI",
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
            'Field'=>"server",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"pi0",
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
            'Default'=>"current_timestamp()",
            'Extra'=>""
        ],[
            'Field'=>"online",
            'Type'=>"tinyint(1)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"1",
            'Extra'=>""
        ],[
            'Field'=>"offline",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"enabled",
            'Type'=>"tinyint(1)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"1",
            'Extra'=>""
        ]
    ];


    private static $servers = null;
    private static function GetInstance(){
        if(is_null(Servers::$servers)) Servers::$servers = new Servers();
        return Servers::$servers;
    }
    public static function GetHub(){
        $servers = Servers::GetInstance();
        $hub = $servers->LoadWhere(['main'=>1,'online'=>1],['server'=>'DESC']);
        if($hub) return $hub;
        $hub = $servers->LoadWhere(['type'=>'hub','online'=>1],['server'=>'DESC']);
        if($hub) return $hub;
        $hub = $servers->ServerMacAddress(LocalMacAddress());
        if($hub) return $hub;
        return ['mac_address'=>LocalMacAddress(),'name'=>Settings::LoadSettingsVar('name','null device'),'url'=>LocalIp(),'type'=>Settings::LoadSettingsVar('type','device'),'server'=>Settings::LoadSettingsVar('server','pi0'),'main'=>Settings::LoadSettingsVar('main',1),'enabled'=>Settings::LoadSettingsVar('enabled',1),'online'=>1];
    }
    public static function GetMain(){
        $servers = Servers::GetInstance();
        $hub = $servers->LoadWhere(['main'=>1],['server'=>'DESC']);
        if($hub) return $hub;
        return null;
    }
    public static function IsHub(){
        $hub = Servers::GetHub();
        return ($hub['mac_address'] == LocalMacAddress());
    }
    public static function OnlineServers(){
        $servers = Servers::GetInstance();
        return $servers->Online();
    }

    public static function ServerIP($ip){
        $servers = Servers::GetInstance();
        return $servers->LoadByUrl($ip);
    }
    public static function ServerMacAddress($mac_address){
        $servers = Servers::GetInstance();
        return $servers->LoadByMacAddress($mac_address);
    }

    public static function SaveServer($data){
        $servers = Servers::GetInstance();
        $data = $servers->CleanData($data);
        $server = $servers->LoadByMacAddress($data['mac_address']);
        if(is_null($server)) $server = $servers->LoadByUrl($data['url']);
        if($server){
            if(isset($data['online'])){
                if((int)$data['online'] == 0){
                    $data['offline'] = $server['offline'] + 1;
                } else {
                    $data['offline'] = 0;
                }
                if($data['offline'] > 2) {
                    $data['online'] = 0;
                } else {
                    $data['online'] = 1;
                }    
            } else {
                if((int)$server['online'] == 0 && isset($data['name'])){
                    $data['online'] = 1;
                    $data['offline'] = 0;
                }
            }
            return $servers->Save($data,['mac_address'=>$data['mac_address']]);
        }
        return $servers->Save($data);
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
    /*
    public function Add($name,$type,$mac_address,$url,$main = 0,$server="pi0w"){
        return $this->Save(['name'=>$name,'type'=>$type,'mac_address'=>$mac_address,'url'=>$url,'main'=>$main,'server'=>$server,'online'=>1,'last_ping'=>date("Y-m-d H:i:s")]);
    }
    public function Update($name,$type,$mac_address,$url, $main = 0,$server="pi0w"){
        return $this->Save(['name'=>$name,'type'=>$type,'url'=>$url,'main'=>$main,'server'=>$server,'online'=>1,'last_ping'=>date("Y-m-d H:i:s")],['mac_address'=>$mac_address]);
    }
    */
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new Servers();
}
?>
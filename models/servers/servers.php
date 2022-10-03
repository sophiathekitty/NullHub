<?php
/**
 * keeps track of the known null devices on the local network
 */
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
            'Field'=>"hub",
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
            'Field'=>"rank",
            'Type'=>"int(11)",
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
            'Field'=>"modified",
            'Type'=>"datetime",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
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
    /**
     * @return Servers|clsModel
     */
    private static function GetInstance(){
        if(is_null(Servers::$servers)) Servers::$servers = new Servers();
        return Servers::$servers;
    }
    /**
     * load the main hub (or at least an online hub if the main is offline)
     * @return array the data array of the server
     */
    public static function GetHub(){
        if(defined("SETUP_MODE")) return ['mac_address'=>LocalMac(),'name'=>'null device','url'=>LocalIp(),'type'=>'device','server'=>'pi','main'=>0,'enabled'=>0,'online'=>0];
        $servers = Servers::GetInstance();
        $hub = $servers->LoadWhere(['main'=>1,'online'=>1],['rank'=>'DESC']);
        if($hub) return $hub;
        $hub = $servers->LoadWhere(['type'=>'hub','online'=>1],['rank'=>'DESC']);
        if($hub) return $hub;
        $hub = $servers->LoadWhere(['type'=>'kiosk','online'=>1],['rank'=>'DESC']);
        if($hub) return $hub;
        $hub = $servers->ServerMacAddress(LocalMac());
        if($hub) return $hub;
        global $device_info;
        if(isset($device_info) && isset($device_info['url'])){
            $url = str_replace(["http:","/"],"",$device_info['url']);
            return ['mac_address'=>"device_info",'name'=>"setup",'url'=>$url,'type'=>"hub",'server'=>"setup",'main'=>0,'enabled'=>1,'online'=>1];
        }
        return ['mac_address'=>LocalMac(),'name'=>Settings::LoadSettingsVar('name','null device'),'url'=>LocalIp(),'type'=>Settings::LoadSettingsVar('type','device'),'server'=>Settings::LoadSettingsVar('server','pi0'),'main'=>Settings::LoadSettingsVar('main',1),'enabled'=>Settings::LoadSettingsVar('enabled',1),'online'=>1];
    }
    /**
     * load the main hub even if it's offline
     * @return array the data array of the server
     */
    public static function GetMain(){
        $servers = Servers::GetInstance();
        $hub = $servers->LoadWhere(['main'=>1],['rank'=>'DESC','online'=>'DESC']);
        Debug::Log("Servers::GetMain",$hub);
        if($hub) return $hub;
        global $device_info;
        if(isset($device_info) && isset($device_info['url'])){
            $url = str_replace(["http:","/"],"",$device_info['url']);
            return ['mac_address'=>"device_info",'name'=>"setup",'url'=>$url,'type'=>"hub",'server'=>"setup",'main'=>0,'enabled'=>1,'online'=>1];
        }
        return null;
    }
    /**
     * is this the hub?
     * @return bool returns true if this is the hub
     */
    public static function IsHub(){
        $hub = Servers::GetHub();
        if($hub['mac_address'] == "device_info") return false;
        return ($hub['mac_address'] == LocalMac());
    }
    /**
     * is this the main hub?
     * @return bool returns true if this is the main hub
     */
    public static function IsMain(){
        $hub = Servers::GetMain();
        return ($hub['mac_address'] == LocalMac()) && Servers::IsHub();
    }
    /**
     * is this the main hub?
     * @return bool returns true if this is the main hub
     */
    public static function CanBeMain($mac_address = null){
        if(is_null($mac_address)) $mac_address = LocalMac();
        $server = Servers::ServerMacAddress($mac_address);
        return ($server['type'] == "hub" || $server['type'] == "kiosk" || $server['type'] == "old_hub");
    }
    /**
     * loads the online servers
     * @return array an array of server arrays
     */
    public static function OnlineServers(){
        if(defined("SETUP_MODE")) return [];
        $servers = Servers::GetInstance();
        return $servers->Online();
    }
    /**
     * load server by ip address
     * @param string $ip the ip address to look up
     * @return array data array for server
     */
    public static function ServerIP($ip){
        $servers = Servers::GetInstance();
        return $servers->LoadByUrl($ip);
    }
    /**
     * load server by mac address
     * @param string $mac_address the mac address to look up
     * @return array data array for server
     */
    public static function ServerMacAddress($mac_address){
        if($mac_address == "local_host"){
            return ['mac_address'=>"device_info",'name'=>"setup",'url'=>'localhost','type'=>"hub",'server'=>"setup",'main'=>1,'enabled'=>1,'online'=>1];
        }
        if($mac_address == "device_info"){
            global $device_info;
            if(isset($device_info) && isset($device_info['url'])){
                $url = str_replace(["http:","/"],"",$device_info['url']);
                return ['mac_address'=>"device_info",'name'=>"setup",'url'=>$url,'type'=>"hub",'server'=>"setup",'main'=>1,'enabled'=>1,'online'=>1];
            }    
        }
        $servers = Servers::GetInstance();
        return $servers->LoadByMacAddress($mac_address);
    }
    /**
     * save a server
     * @param array $data the server's data array to save
     * @return array a save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveServer($data){
        if($data['mac_address'] == 'device_info') return ['error'=>'device_info'];
        $servers = Servers::GetInstance();
        $data = $servers->CleanData($data);
        $server = $servers->LoadByMacAddress($data['mac_address']);
        if(is_null($server)) $server = $servers->LoadByUrl($data['url']);
        if($server){
            Debug::Log("Servers::SaveServer","Server Exists already",$data);
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
            //echo "\n\nsave existing server\n";
            //print_r($server);
            //print_r($data);
            if($server['mac_address'] != $data['mac_address']){
                if($server['name'] != $data['name'] && $server['server'] != $data['server']) return $servers->Save($data);
                return $servers->Save($data,['url'=>$data['url']]);
            }
            return $servers->Save($data,['mac_address'=>$data['mac_address']]);
        }
        Debug::Log("Servers::SaveServer","Save new server",$data);
        return $servers->Save($data);
    }
    /**
     * loads the online servers
     * @return array an array of server arrays
     */
    public function Online(){
        return $this->LoadAllWhere(['online'=>1],["last_ping"=>"DESC"]);
    }
    /**
     * loads the offline servers
     * @return array an array of server arrays
     */
    public function Offline(){
        return $this->LoadAllWhere(['online'=>0]);
    }
    /**
     * load server by ip address
     * @param string $ip the ip address to look up
     * @return array data array for server
     */
    public function LoadByUrl($ip){
        if(strpos($ip,"::1") > -1 || $ip == "localhost"){
            return ['id'=>0,'name'=>'localhost','url'=>$ip,'mac_address'=>"localhost"];
        }
        return $this->LoadWhere(['url'=>$ip]);
    }
    /**
     * load server by mac address
     * @param string $mac_address the mac address to look up
     * @return array data array for server
     */
    public function LoadByMacAddress($mac_address){
        return $this->LoadWhere(['mac_address'=>$mac_address]);
    }
    /**
     * ping a server by mac address
     * @param string $mac_address the mac address to look up
     * @return array data array for server
     */
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
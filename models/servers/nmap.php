<?php
/**
 * keeps track of all the know ip addresses
 */
class nMap extends clsModel{
    public $table_name = "nMap";
    public $fields = [
        [
            'Field'=>"ip",
            'Type'=>"varchar(50)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"type",
            'Type'=>"varchar(10)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"new",
            'Extra'=>""
        ],[
            'Field'=>"created",
            'Type'=>"datetime",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"current_timestamp()",
            'Extra'=>""
        ],[
            'Field'=>"modified",
            'Type'=>"datetime",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"current_timestamp()",
            'Extra'=>"on update current_timestamp()"
        ]
    ];


    private static $servers = null;
    /**
     * @return nMap|clsModel
     */
    private static function GetInstance(){
        if(is_null(nMap::$servers)) nMap::$servers = new nMap();
        return nMap::$servers;
    }
    /**
     * loads the next ip address to check
     * @return array data array for an nMap record
     */
    public static function LoadNext(){
        $nmap = nMap::GetInstance();
        $host = $nmap->LoadWhere(['type'=>'new'],['created'=>'ASC']);
        if($host) return $host;
        $host = $nmap->LoadWhere(['type'=>'pi'],['modified'=>'ASC']);
        if($host) return $host;
        $rows = clsDB::$db_g->select("SELECT * FROM `".$nmap->table_name."` WHERE `type` NOT LIKE 'unknown' ORDER BY `modified` ASC LIMIT 1;");
        if(count($rows) > 0) return $rows[0];
        $rows = clsDB::$db_g->select("SELECT * FROM `".$nmap->table_name."` ORDER BY `modified` ASC LIMIT 1;");
        if(count($rows) > 0) return $rows[0];
        return null;
    }
    /**
     * save an nMap host
     * @param array data array for an nMap host
     * @return array a save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveHost($host){
        $nmap = nMap::GetInstance();
        $h = $nmap->LoadWhere(['ip'=>$host['ip']]);
        $host = $nmap->CleanData($host);
        $host['modified'] = date("Y-m-d H:i:s");
        if($h){
            return $nmap->Save($host,['ip'=>$host['ip']]);
        }
        return $nmap->Save($host);
    }
    /**
     * load a host by ip address
     * @param string $ip the ip address of the host to load
     * @return array nmap host with $ip
     */
    public static function LoadByIp($ip){
        $nmap = nMap::GetInstance();
        return $nmap->LoadWhere(['ip'=>$ip]);
    }
    /**
     * forget the unknown hosts
     */
    public static function ForgetUnknown(){
        $nmap = nMap::GetInstance();
        $nmap->DeleteFieldValue('type','unknown');
    }
    /**
     * forget the pi hosts
     */
    public static function ForgetPi(){
        $nmap = nMap::GetInstance();
        $nmap->DeleteFieldValue('type','pi');
    }
    /**
     * forget the wemos
     */
    public static function ForgetWeMo(){
        $nmap = nMap::GetInstance();
        $nmap->DeleteFieldValue('type','wemo');
    }
    /**
     * forget all of the hosts
     */
    public static function ForgetAll(){
        $nmap = nMap::GetInstance();
        $nmap->Truncate();
    }
}

if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new nMap();
}

?>
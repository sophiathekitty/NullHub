<?php
/**
 * keeps track of the known null devices on the local network
 */
class HubCandidates extends clsModel{
    public $table_name = "HubCandidates";
    public $fields = [
        [
            'Field'=>"mac_address",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"latency",
            'Type'=>"float",
            'Null'=>"Yes",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"plugins",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"extensions",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"score",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
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
     * @return HubCandidates|clsModel
     */
    private static function GetInstance(){
        if(is_null(HubCandidates::$servers)) HubCandidates::$servers = new HubCandidates();
        return HubCandidates::$servers;
    }
    /**
     * load all candidates
     * @return array the data array of the server
     */
    public static function ClearCandidates(){
        $instance = HubCandidates::GetInstance();
        return $instance->Truncate();
    }
    /**
     * load all candidates
     * @return array the data array of the server
     */
    public static function AllCandidates(){
        $instance = HubCandidates::GetInstance();
        return $instance->LoadAll(['score'=>'DESC']);
    }
    /**
     * load the top candidate
     * @return array the data array of the server
     */
    public static function TopCandidate(){
        $instance = HubCandidates::GetInstance();
        $candidates = $instance->LoadAll(['score'=>'DESC']);
        foreach($candidates as $candidate){
            return $candidate;
        }
        return null;
    }
    /**
     * load candidate by mac address
     * @param string $mac_address the mac address of the hub candidate
     * @return array the data array of the server
     */
    public static function MacAddress($mac_address){
        $instance = HubCandidates::GetInstance();
        return $instance->LoadWhere(['mac_address'=>$mac_address]);
    }
    /**
     * load all candidates
     * @param array $data data array for a hub candidate
     * @return array the save report
     */
    public static function SaveCandidates($data){
        $instance = HubCandidates::GetInstance();
        $data = $instance->CleanData($data);
        $candidate = HubCandidates::MacAddress($data['mac_address']);
        if(is_null($candidate)){
            return $instance->Save($data);
        }
        return $instance->Save($data,['mac_address'=>$data['mac_address']]);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new HubCandidates();
}
?>
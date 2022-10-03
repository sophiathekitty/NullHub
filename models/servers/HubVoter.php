<?php
/**
 * keeps track of the known null devices on the local network
 */
class HubVoters extends clsModel{
    public $table_name = "HubVoter";
    public $fields = [
        [
            'Field'=>"mac_address",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"type",
            'Type'=>"varchar(100)",
            'Null'=>"Yes",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"hub",
            'Type'=>"varchar(100)",
            'Null'=>"Yes",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"vote",
            'Type'=>"varchar(100)",
            'Null'=>"Yes",
            'Key'=>"",
            'Default'=>null,
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


    private static $instance = null;
    /**
     * @return HubVoters|clsModel
     */
    private static function GetInstance(){
        if(is_null(HubVoters::$instance)) HubVoters::$instance = new HubVoters();
        return HubVoters::$instance;
    }
    /**
     * load all Voters
     * @return array the data array of the server
     */
    public static function ClearVoters(){
        $instance = HubVoters::GetInstance();
        return $instance->Truncate();
    }
    /**
     * load all Voters
     * @return array the data array of the server
     */
    public static function AllVoters(){
        $instance = HubVoters::GetInstance();
        return $instance->LoadAll();
    }
    /**
     * load vote by mac address
     * @param string $mac_address the mac address of the hub vote
     * @return array the data array of the server
     */
    public static function MacAddress($mac_address){
        $instance = HubVoters::GetInstance();
        return $instance->LoadWhere(['mac_address'=>$mac_address]);
    }
    /**
     * load all Voters
     * @param array $data data array for a hub vote
     * @return array the save report
     */
    public static function SaveVoter($data){
        $instance = HubVoters::GetInstance();
        $data = $instance->CleanData($data);
        $vote = HubVoters::MacAddress($data['mac_address']);
        if(is_null($vote)){
            return $instance->Save($data);
        }
        return $instance->Save($data,['mac_address'=>$data['mac_address']]);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new HubVoters();
}
?>
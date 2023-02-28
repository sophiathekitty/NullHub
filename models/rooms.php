<?php
/**
 * rooms database table stuff
 */
class Rooms extends clsModel{
    public $table_name = "Rooms";
    public $fields = [
        [
            'Field'=>"id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>"auto_increment"
        ],[
            'Field'=>"name",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"floor",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"public",
            'Type'=>"tinyint(1)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"1",
            'Extra'=>""
        ],[
            'Field'=>"bedtime",
            'Type'=>"time",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"awake_time",
            'Type'=>"time",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"activity",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"light_level",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"sunlight_offset",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"2",
            'Extra'=>""
        ],[
            'Field'=>"hide_room",
            'Type'=>"tinyint(1)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"lights_on_in_room",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"lights_on_in_neighbors",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"neighbors_lights_off_percent",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"sunrise",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"sunset",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"IsTimeToGetUp",
            'Type'=>"tinyint(1)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"IsTimeForBed",
            'Type'=>"tinyint(1)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"IsBedtimeHours",
            'Type'=>"tinyint(1)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"IsDayInside",
            'Type'=>"tinyint(1)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"IsDayTime",
            'Type'=>"tinyint(1)",
            'Null'=>"YES",
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


    private static $rooms = null;
    /**
     * @return Rooms|clsModel
     */
    private static function GetInstance(){
        if(is_null(Rooms::$rooms)) Rooms::$rooms = new Rooms();
        return Rooms::$rooms;
    }
    /**
     * save a room
     * @param array $data the room data array
     * @return array a save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveRoom($data){
        $rooms = Rooms::GetInstance();
        $data = $rooms->CleanData($data);
        $room = $rooms->LoadById($data['id']);
        Debug::Log("Rooms::SaveRoom",$data);
        if(is_null($room)){
            return $rooms->Save($data);
        } else {
            $room['modified'] = date("Y-m-d H:i:s");
            return $rooms->Save($data,['id'=>$data['id']]);
        }
    }
    /**
     * load all the rooms
     * @return array array of all the rooms
     */
    public static function AllRooms(){
        $rooms = Rooms::GetInstance();
        return $rooms->LoadAll();
    }
        /**
     * load all the rooms
     * @return array array of all the rooms
     */
    public static function ActiveRooms(){
        $rooms = Rooms::GetInstance();
        return $rooms->LoadAllWhere(["hide_room"=>'0']);
    }
    /**
     * load a room by id
     * @param int $id the room id
     * @return array the data array for a room 
     */
    public static function RoomId($id){
        $rooms = Rooms::GetInstance();
        return $rooms->LoadById($id);
    }
    /**
     * load a room by name
     * @param string $name the room name
     * @return array the data array for a room
     */
    public static function RoomName($name){
        $rooms = Rooms::GetInstance();
        return $rooms->LoadWhere(['name'=>$name]);
    }
    /**
     * load the rooms for a set floor
     * @param string|int $floor the floor # you want to load... or ground (0) and basement (-1)
     * @param int|null $public set to 1 to only return public (shared/common) rooms...
     * @return array array of rooms for a set floor ['floor'=>$floor,'rooms'=>$rooms->LoadAllWhere(['floor'=>$f])]
     */
    public static function Floor($floor, $public = null){
        $f = 0;
        if($floor == "second") $f = 1;
        if($floor == "ground") $f = 0;
        if($floor == "basement") $f = -1;
        $rooms = Rooms::GetInstance();
        if(is_null($public)) return ['floor'=>$floor,'rooms'=>$rooms->LoadAllWhere(['floor'=>$f,'hide_room'=>'0'])];
        return ['floor'=>$floor,'rooms'=>$rooms->LoadAllWhere(['floor'=>$f,'public'=>$public,'hide_room'=>'0'])];
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new Rooms();
}
?>
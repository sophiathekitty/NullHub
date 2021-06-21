<?php
class RoomNeighbors extends clsModel{
    public $table_name = "RoomNeighbors";
    public $fields = [
        [
            'Field'=>"id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>"auto_increment"
        ],[
            'Field'=>"room_id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"neighbor_id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ]
    ];


    private static $rooms = null;
    private static function GetInstance(){
        if(is_null(RoomNeighbors::$rooms)) RoomNeighbors::$rooms = new RoomNeighbors();
        return RoomNeighbors::$rooms;
    }
    /**
     * save a room neighbor relation
     * @param array $data json array of room neighbor 
     */
    public static function SaveNeighbor($data){
        $rooms = RoomNeighbors::GetInstance();
        $data = $rooms->CleanData($data);
        $room = $rooms->LoadById($data['id']);
        //print_r($data);
        if(is_null($room)){
            $rooms->Save($data);
        } else {
            $rooms->Save($data,['id'=>$data['id']]);
        }
    }
    /**
     * get the neighbors of a room
     * @param int $room_id the current room
     * @return array an array of room ids for neighboring rooms
     */
    public static function Neighbors($room_id){
        $rooms = RoomNeighbors::GetInstance();
        return $rooms->LoadAllWhere(['room_id'=>$room_id]);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new Rooms();
}
?>
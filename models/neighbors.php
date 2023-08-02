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
    /**
     * @return RoomNeighbors|clsModel
     */
    private static function GetInstance(){
        if(is_null(RoomNeighbors::$rooms)) RoomNeighbors::$rooms = new RoomNeighbors();
        return RoomNeighbors::$rooms;
    }
    /**
     * save a room neighbor relation
     * @param array $data json array of room neighbor 
     * @todo maybe after i'm sure i'm never going to need the old hub i can take out the stuff
     * for legacy syncing....
     */
    public static function SaveNeighbor($data){
        $rooms = RoomNeighbors::GetInstance();
        $data = $rooms->CleanData($data);
        // for legacy syncing...
        $room = $rooms->LoadWhere(['room_id'=>$data['room_id'],'neighbor_id'=>$data['neighbor_id']]);
        if(is_null($room)){
            // ok now but for non legacy syncing...
            $room = $rooms->LoadById($data['room_id']);
            if(is_null($room)){
                $rooms->Save($data);
            } else {
                $rooms->Save($data,['id'=>$data['id']]);
            }
        }
    }
    /**
     * get the neighbors of a room
     * @param int $room_id the current room
     * @return array an array of room ids for neighboring rooms
     */
    public static function Neighbors($room_id){
        $rooms = RoomNeighbors::GetInstance();
        $neighbors = $rooms->LoadAllWhere(['room_id'=>$room_id]);
        if(count($neighbors) == 0) $neighbors = $rooms->LoadAllWhere(['neighbor_id'=>$room_id]);
        return $neighbors;
    }
    /**
     * get all the neighbors (like for syncing)
     * @return array all the neighbors
     */
    public static function AllNeighbors(){
        $rooms = RoomNeighbors::GetInstance();
        return $rooms->LoadAll();
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new RoomNeighbors();
}
?>
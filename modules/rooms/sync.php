<?php
/**
 * sync all rooms
 * @return array|null returns array of rooms or null if this is the main hub or it couldn't find the main hub
 */
function SyncRooms(){
    //if(Settings::LoadSettingsVar('main',0)) return null;
    if(Servers::IsMain()) return null;
    //$hub = Servers::GetHub();
    //if(is_null($hub)) return null;
    //$url = "http://".$hub['url']."/api/rooms/";
    //$url = "http://".$hub['url']."/api/rooms/?simple=1";
    //$url = "http://".$hub['url']."/api/rooms/?room_id=".Settings::LoadSettingsVar('room_id');
    $data = ServerRequests::LoadMainJSON("/api/rooms/");
    Debug::Log("SyncRoom",$data);
    return SyncRoomUrl($data);
}
/**
 * sync room for this device. should probably make sure a room_id has been set in the settings vars
 * @return array|null returns data array of room or null if this is the main hub or it couldn't find the main hub
 */
function SyncRoom(){
    //if(Settings::LoadSettingsVar('main',0)) return null;
    if(Servers::IsMain()) return null;
    $room_id = (int)Settings::LoadSettingsVar('room_id',0);
    if($room_id == 0) return null;//SyncRooms();
    //$hub = Servers::GetHub();
    //if(is_null($hub)) return null;
    //$url = "http://".$hub['url']."/api/rooms/?room_id=".Settings::LoadSettingsVar('room_id');
    $data = ServerRequests::LoadMainJSON("/api/rooms/?room_id=".$room_id);
    Debug::Log("SyncRoom",$data);
    return SyncRoomUrl($data);
}
/**
 * parses the data now instead of loading now that i'm using ServerRequests::LoadMainJSON
 * @param array $data the data array loaded from the main hub
 * @return array|null all the rooms saved locally or null if the data was null
 */
function SyncRoomUrl($data){
    if(is_null($data)) return null;
    //$info = file_get_contents($url);
    //$data = json_decode($info,true);
    //print_r($data);
    if(isset($data['rooms'])){
        foreach($data['rooms'] as $room){
            // save room
            Rooms::SaveRoom($room);
            $err = clsDB::$db_g->get_err();
            if($err != "") Debug::Log("SyncRoomUrl",$err);
        }    
    }
    if(isset($data['room'])){
        $res = Rooms::SaveRoom(($data['room']));
        $err = clsDB::$db_g->get_err();
        if($err != "") Debug::Log("SyncRoomUrl",$res,$err);
    }
    return Rooms::AllRooms();
}
/**
 * sync neighbors
 */
function SyncNeighbors(){
    $hub = Servers::GetHub();
    if($hub['type'] == "old_hub"){
        // handle syncing from old hub
        $rooms = Rooms::AllRooms();
        foreach($rooms as $room){
            SyncNeighborsLegacy($room['id']);
        }
    } else {
        $data = ServerRequests::LoadMainJSON("/api/rooms/neighbors/");
        if(is_array($data) && isset($data['neighbors'])){
            foreach($data['neighbors'] as $neighbor){ 
                RoomNeighbors::SaveNeighbor($neighbor);
            }
        }
    }
}
/**
 * sync neighbors from old hub
 */
function SyncNeighborsLegacy($room_id){
    $data = ServerRequests::LoadMainJSON("/api/rooms/?room_id=$room_id&neighbors=1");
    if(is_array($data) && isset($data['neighbors'])){
        foreach($data['neighbors'] as $neighbor_id){
            RoomNeighbors::SaveNeighbor(['room_id'=>$room_id,'neighbor_id'=>$neighbor_id]);
        }
    }
}
?>
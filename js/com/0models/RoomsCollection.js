/**
 * loads in a list of room data
 */
class RoomsCollection extends Collection {
    static instance = new RoomsCollection();
    constructor(){
        super("rooms","room","/api/rooms/","/api/rooms/save")
    }
    /**
     * get the rooms on the first floor (ground level)
     * @param {Function(json)} callBack list of rooms
     */
    firstFloor(callBack){
        this.floor(0,callBack);
    }
    /**
     * get the rooms in the basement
     * @param {Function(json)} callBack list of rooms
     */
    basement(callBack){
        this.floor(-1,callBack);
    }
    /**
     * get the rooms on a specified floor
     * @param {Number} level the floor
     * @param {Function(JSON)} callBack list of rooms
     */
    floor(level,callBack){
        this.getData(json=>{
            var rooms = [];
            json.rooms.forEach(room=>{
                if(Number(room.floor) == level) rooms.push(room);
            });
            callBack(rooms);
        });
    }
}
class RoomsCollection extends Collection {
    static instance = new RoomsCollection();
    constructor(){
        super("rooms","room","/api/rooms/","/api/rooms/save")
    }
    firstFloor(callBack){
        this.floor(0,callBack);
    }
    basement(callBack){
        this.floor(-1,callBack);
    }
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
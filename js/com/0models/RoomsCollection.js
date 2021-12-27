class RoomsCollection extends Collection {
    constructor(){
        super("rooms","room","/api/rooms/floors","/api/rooms/save");
    }
}
class RoomsList extends View {
    constructor(){
        super(RoomsCollection.instance,new Template("rooms_list","/templates/sections/rooms.html"),new Template("rooms_item","/templates/items/room.html"));
    }
    build(){
        this.display();
    }
    display(){

    }
}
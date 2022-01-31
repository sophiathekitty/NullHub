/**
 * Room list view handler
 */
class RoomsList extends View {
    constructor(debug = true){
        if(debug) console.log("RoomsList::Constructor");
        super(new RoomsCollection(),new Template("rooms_list","/templates/sections/rooms.html"),new Template("rooms_item","/templates/items/room.html"));
    }
    /**
     * builds the room list
     */
    build(){
        if(this.debug) console.log("RoomsList::Build");
        if(this.template){
            this.template.getData(html=>{
                if(this.debug) console.log("RoomsList::Build-template",html);
                $(html).appendTo(".app main");
                if(this.item_template && this.model){
                    this.item_template.getData(itm_html=>{
                        if(this.debug) console.log("RoomsList::Build-item_template",itm_html);
                        this.model.getData(json=>{
                            if(this.debug) console.log("RoomsList::Build-data",json);
                            json.rooms.forEach((room,index)=>{
                                if(this.debug) console.log("RoomsList::Build-room",room);
                                $(itm_html).appendTo("#rooms_list ul[collection=rooms]").attr("index",index).attr("room_id",room.id).attr("level",room.floor);
                            });
                            this.display();
                        });
                    });
                }
            });
        }
    }
    /**
     * populate the data for room list
     */
    display(){
        if(this.debug) console.log("RoomsList::Display");
        if(this.model){
            this.model.getData(json=>{
                if(this.debug) console.log("RoomsList::Display-data",json);
                json.rooms.forEach(room=>{
                    if(this.debug) console.log("RoomsList::Display-room",room);
                    $("#rooms_list [room_id="+room.id+"] [var=name]").html(room.name);
                    $("#rooms_list [room_id="+room.id+"] [var=lights_on_in_room]").attr("val",room.lights_on_in_room);
                    $("#rooms_list [room_id="+room.id+"] [var=lights_on_in_neighbors]").attr("val",room.lights_on_in_neighbors);
                });
            });
        }
    }
}
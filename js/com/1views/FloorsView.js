class FloorsView extends View {
    constructor(debug = false, refresh = 60000){
        if(debug) console.log("FloorsView::Constructor");
        super(RoomsCollection.instance,new Template("floors","/templates/sections/floors.html"),new Template("room_card","/templates/stamps/room.html"),refresh,debug);
    }
    build(){
        if(this.debug) console.log("FloorsView::Build");
        if(this.template && this.model){
            this.template.getData(html=>{
                if(this.debug) console.log("FloorsView::Build-template",html);
                $("main.contents #floors").remove();
                $(html).appendTo("main.contents");
                this.model.getData(json=>{
                    if(this.debug) console.log("FloorsView::Build-data",json);
                    this.item_template.getData(itm_html=>{
                        if(this.debug) console.log("FloorsView::Build-item_template",itm_html);
                        json.rooms.forEach(room=>{
                            if(this.debug) console.log("FloorsView::Build-room",room.floor,room);
                            $(itm_html).appendTo("#floors [level="+room.floor+"]").attr("room_id",room.id);
                        });
                    this.display();
                    if(this.controller) this.controller.roomsBuilt();
                    });
                });
            });
        }
    }
    display(){
        if(this.debug) console.log("FloorsView::Display");
        if(this.model){
            this.model.getData(json=>{
                if(this.debug) console.log("FloorsView::Display-data",json);
                json.rooms.forEach(room=>{
                    if(this.debug) console.log("FloorsView::Display-room",room);
                    $("#floors [room_id="+room.id+"]").attr("activity",room.activity);
                    $("#floors [room_id="+room.id+"]").attr("lights_on_in_room",room.lights_on_in_room);
                    $("#floors [room_id="+room.id+"]").attr("lights_on_in_neighbors",room.lights_on_in_neighbors);
                    $("#floors [room_id="+room.id+"]").attr("neighbors_lights_off_percent",room.neighbors_lights_off_percent);
                    $("#floors [room_id="+room.id+"] [var=name]").html(room.name);
                    // room time schedule sensors
                    if(room.bedtime){
                        if(room.IsTimeForBed){
                            $("#floors [room_id="+room.id+"] [var=IsTimeForBed]").attr("val","1");
                            $("#floors [room_id="+room.id+"] [var=IsTimeForBed]").attr("title","Is Time For Bed In "+room.name);
                        } else {
                            $("#floors [room_id="+room.id+"] [var=IsTimeForBed]").attr("val","0");
                            $("#floors [room_id="+room.id+"] [var=IsTimeForBed]").attr("title","Not Time For Bed In "+room.name);
                        }
                        if(room.IsBedtimeHours){
                            $("#floors [room_id="+room.id+"] [var=IsBedtimeHours]").attr("val","1");
                            $("#floors [room_id="+room.id+"] [var=IsBedtimeHours]").attr("title","Is Bedtime Hours In "+room.name);
                        } else {
                            $("#floors [room_id="+room.id+"] [var=IsBedtimeHours]").attr("val","0");
                            $("#floors [room_id="+room.id+"] [var=IsBedtimeHours]").attr("title","Not Bedtime Hours In "+room.name);
                        }
                        if(room.IsTimeToGetUp){
                            $("#floors [room_id="+room.id+"] [var=IsTimeToGetUp]").attr("val","1");
                            $("#floors [room_id="+room.id+"] [var=IsTimeToGetUp]").attr("title","Is Time To Get Up In "+room.name);
                        } else {
                            $("#floors [room_id="+room.id+"] [var=IsTimeToGetUp]").attr("val","0");    
                            $("#floors [room_id="+room.id+"] [var=IsTimeToGetUp]").attr("title","Not Time To Get Up In "+room.name);
                        }
                    }
                    if(room.IsDayInside){
                        $("#floors [room_id="+room.id+"] [var=IsDayInside]").attr("val","1");
                        $("#floors [room_id="+room.id+"] [var=IsDayInside]").attr("title","Is Daylight Inside Of "+room.name);
                    } else {
                        $("#floors [room_id="+room.id+"] [var=IsDayInside]").attr("val","0");
                        $("#floors [room_id="+room.id+"] [var=IsDayInside]").attr("title","Not Daylight Inside Of "+room.name);
                    }
                    if(room.IsDayTime){
                        $("#floors [room_id="+room.id+"] [var=IsDayTime]").attr("val","1");
                        $("#floors [room_id="+room.id+"] [var=IsDayTime]").attr("title","Is Daylight Outside");
                    } else {
                        $("#floors [room_id="+room.id+"] [var=IsDayTime]").attr("val","0");
                        $("#floors [room_id="+room.id+"] [var=IsDayTime]").attr("title","Not Daylight Outside");
                    }
                });
            });
        }
    }
    refresh(){
        if($("#floors").length){
            this.display();
        } else if($("main.contents").length) this.build();
    }
}
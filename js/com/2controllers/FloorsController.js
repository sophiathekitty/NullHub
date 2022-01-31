/**
 * controller for the floors view
 */
class FloorsController extends Controller {
    static instance = new FloorsController();
    /**
     * constructor
     * @param {bool} debug show console output
     */
    constructor(debug = true){
        if(debug) console.log("FloorsController::Constructor");
        super(new FloorsView(),debug);
        this.lights = null;
        this.temperature = null;
        this.displays = null;
    }
    /**
     * build view and start refresh interval
     */
    ready(){
        if(this.debug) console.log("FloorsController::Ready");
        if($("main.contents").length){
            this.view.build();
            this.refreshInterval();
        }
    }
    /**
     * handles when room name clicked
     */
    clickRoomHeader(){
        if(this.debug) console.log("FloorsController::clickRoomHeader");
        this.click("#floors","[var=name]",e=>{
            e.preventDefault();
            var room_id = $(e.currentTarget).parent().parent().attr("room_id");
            var level = $(e.currentTarget).parent().parent().parent().attr("level");
            if(this.debug) console.log("FloorsController::ClickRoomHeader:room_id",room_id,level);
            if($("main").hasClass("show_details")){
                $("#floors").removeAttr("room_id");
                $("#floors").removeAttr("level");
                $("main").removeClass("show_details");    
            } else {
                $("#floors").attr("room_id",room_id);
                $("#floors").attr("level",level);
                $("main").addClass("show_details");    
            }
        });
    }
    /**
     * once the rooms have been built see about adding plugin elements
     */
    roomsBuilt(){
        try {
            this.lights = new RoomLightsController();
            if(this.debug) console.log("FloorController::RoomsBuilt-RoomLightsController",this.lights);
            this.view.model.getData(json=>{
                json.rooms.forEach(room=>{
                    this.lights.view.build(room.id);
                });
            });
            this.lights.roomsReady();
        } catch (error) {
            if(this.debug) console.warn("FloorsController::RoomsBuilt-RoomLightsController not available",error);
        }
        try {
            this.displays = new DisplayStatusIcons();
            if(this.debug) console.log("FloorController::RoomsBuilt-DisplayStatusIcons",this.displays);
            this.view.model.getData(json=>{
                json.rooms.forEach(room=>{
                    this.displays.build(room.id);
                });
            });
        } catch (error) {
            if(this.debug) console.warn("FloorsController::RoomsBuilt-DisplayStatusIcons not available",error);
        }
        try {
            this.temperature = new TemperatureBug();
            if(this.debug) console.log("FloorController::RoomsBuilt-TemperatureBug",this.temperature);
            this.view.model.getData(json=>{
                json.rooms.forEach(room=>{
                    this.temperature.build(room.id);
                });
            });
        } catch (error) {
            if(this.debug) console.warn("FloorsController::RoomsBuilt-TemperatureBug not available",error);
        }
    }
    /**
     * handles refreshing the floors views
     */
    refresh(){
        this.view.refresh();
        if(this.temperature){
            this.view.model.getData(json=>{
                json.rooms.forEach(room=>{
                    this.temperature.refresh(room.id);
                });
            });
        }
        if(this.displays){
            this.view.model.getData(json=>{
                json.rooms.forEach(room=>{
                    this.displays.display(room.id);
                });
            });
        }
    }
}
class FloorsController extends Controller {
    static instance = new FloorsController();
    constructor(debug = true){
        if(debug) console.log("FloorsController::Constructor");
        super(new FloorsView(),debug);
        this.lights = null;
        this.temperature = null;
    }
    ready(){
        if(this.debug) console.log("FloorsController::Ready");
        if($("main.contents").length){
            this.view.build();
            this.refreshInterval();
        }
    }
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
    refresh(){
        this.view.refresh();
        if(this.temperature){
            this.view.model.getData(json=>{
                json.rooms.forEach(room=>{
                    this.temperature.refresh(room.id);
                });
            });
        }
    }
}
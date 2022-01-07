class RoomsListController extends Controller {
    static instance = new RoomsListController();
    constructor(debug = true){
        if(debug) console.log("RoomsListController::Constructor");
        super(new RoomsList(),debug);
    }
    ready(){
        if(this.debug) console.log("RoomsListController::Ready");
        this.view.build();
    }
}
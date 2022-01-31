/**
 * room list controller
 */
class RoomsListController extends Controller {
    static instance = new RoomsListController();
    constructor(debug = true){
        if(debug) console.log("RoomsListController::Constructor");
        super(new RoomsList(),debug);
    }
    /**
     * build room list view when document ready
     */
    ready(){
        if(this.debug) console.log("RoomsListController::Ready");
        this.view.build();
        this.click("main",".room a[var=name]",e=>{
            var room_id = $(e.currentTarget).parent().attr("room_id");
            var level = $(e.currentTarget).parent().attr("level");
            if(this.debug) console.log("RoomsListController::Click",room_id,level);
            $("#floors").attr("room_id",room_id);
            $("#floors").attr("level",level);
            $("main").attr("view","rooms");
            $("main").addClass("show_details");
        });
    }
}
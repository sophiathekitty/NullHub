/**
 * AppController
 */
class AppController extends Controller {
    static instance = new AppController();
    constructor(debug = false){
        if(debug) console.log("AppController::Constructor");
        super(new AppView(),debug);
        this.clock = new ClockController();
    }
    /**
     * setup view stuff and section nav handling when document ready
     */
    ready(){
        if(this.debug) console.log("AppController::Ready");
        this.view.build();
    }
}
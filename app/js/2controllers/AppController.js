class AppController extends Controller {
    static instance = new AppController();
    constructor(debug = true){
        if(debug) console.log("AppController::Constructor");
        super(new AppView(),debug);
        this.clock = new ClockController();
    }
    ready(){
        if(this.debug) console.log("AppController::Ready");
        this.view.build();
        this.click("nav.sections","a",e=>{
            if(this.debug) console.log("AppController::nav.sections a::Click",$(e.currentTarget).attr("section"));
            e.preventDefault();
            $("main").attr("view",$(e.currentTarget).attr("section"));
        });
    }
}
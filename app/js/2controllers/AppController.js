class AppController extends Controller {
    static instance = new AppController();
    constructor(){
        super(new InfoView());
        this.clock = new ClockController();
    }
    ready(){
        this.view.build();
    }
}
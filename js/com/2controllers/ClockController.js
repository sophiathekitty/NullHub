class ClockController extends Controller {
    constructor(){
        console.log("ClockController::Constructor");
        super(new ClockView());
    }
    ready(){
        console.log("ClockController::Ready");
        this.view.build();
        if(this.interval) clearTimeout(this.interval);
        this.interval = setTimeout(this.refresh.bind(this),60000);
        this.click(".clock",e=>{
            this.slideshow();
            if(this.interval) clearTimeout(this.interval);
            this.interval = setTimeout(this.refresh.bind(this),240000);    
        });
    }
    refresh(){
        this.slideshow();
        if(this.interval) clearTimeout(this.interval);
        this.interval = setTimeout(this.refresh.bind(this),120000);
    }
    slideshow(){
        if($(".clock").attr("show") == "date"){
            $(".clock").attr("show","sunrise");
        } else {
            $(".clock").attr("show","date");
        }
    }
}
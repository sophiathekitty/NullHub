/**
 * handles the slideshow and interaction with the clock widget
 */
class ClockController extends Controller {
    /**
     * constructor
     * @param {bool} debug show console output
     */
    constructor(debug = true){
        if(debug) console.log("ClockController::Constructor");
        super(new ClockView(),debug);
        this.first_ready = true;
    }
    /**
     * build the view and setup the slideshow timeout and manual progression
     */
    ready(){
        if(this.first_ready){
            if(this.debug) console.log("ClockController::Ready");
            this.view.build(done=>{
                //if(this.interval) clearTimeout(this.interval);
                //this.interval = setTimeout(this.refresh.bind(this),60000);
                this.click(".clock",e=>{
                    this.slideshow();
                    if(this.interval) clearTimeout(this.interval);
                    this.interval = setTimeout(this.refresh.bind(this),240000);    
                });
                this.refreshInterval(60000);
            });
            this.first_ready = false;
        }
    }
    /**
     * slideshow timeout handler
     */
    refresh(){
        this.slideshow();
        this.view.display();
        //if(this.interval) clearTimeout(this.interval);
        //this.interval = setTimeout(this.refresh.bind(this),120000);
        this.refreshInterval(120000);

    }
    /**
     * switch between showing the date and sunrise
     */
    slideshow(){
        if($(".clock").attr("NullWeather")){
            switch($(".clock").attr("show")){
                case "date":
                    $(".clock").attr("show","sunrise");
                    break;
                case "sunrise":
                    $(".clock").attr("show","feels_like");
                    break;
                case "feels_like":
                    $(".clock").attr("show","humidity");
                    break;
                case "humidity":
                    $(".clock").attr("show","wind_speed");
                    break;
                case "wind_speed":
                    $(".clock").attr("show","description");
                    break;
                case "description":
                    $(".clock").attr("show","date");
                    break;
            }
        } else {
            if($(".clock").attr("show") == "date"){
                $(".clock").attr("show","sunrise");
            } else {
                $(".clock").attr("show","date");
            }    
        }
    }
}
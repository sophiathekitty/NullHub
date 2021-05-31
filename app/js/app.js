class AppController extends Controller  {
    constructor(){
        super(new AppView());
    }
    ready(){
        this.view.build();
    }
}
var app = new AppController();
$(document).ready(function(){
    app.ready();
});
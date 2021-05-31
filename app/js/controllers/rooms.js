class RoomsController extends Controller{
    ready(){
        console.log("rooms ready?");
    }
}
var roomsController = new RoomsController(new View());
$(document).ready(function(){
    roomsController.ready();
});
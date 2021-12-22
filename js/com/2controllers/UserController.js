class UserController extends Controller {
    constructor(){
        super(new UserView());
    }
    ready(){
        this.view.build();
    }
}
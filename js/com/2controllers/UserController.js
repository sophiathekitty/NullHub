/**
 * handles the user events
 */
class UserController extends Controller {
    static user = new UserController();
    constructor(debug = false){
        super(new UserView(),debug);
        this.first_ready = true;
    }
    /**
     * builds the user view and sets up the event handlers
     */
    ready(){
        if(this.first_ready){
            this.view.build();
            this.setupUserEvents();
            this.first_ready = false;    
        }
    }
    /**
     * sets up the user event handlers
     */
    setupUserEvents(){
        this.click("#user a",e=>{
            if(this.debug) console.log($(e.currentTarget).attr("action"));
            switch($(e.currentTarget).attr("action")){
                case "logout":
                    this.view.model.logout(json=>{
                        this.view.display();
                    });
                    break;
                case "login":
                    this.login();
                    break;
                case "signup":
                    this.signup();
                    break;
                case "cancel":
                    $("#user").attr("login","false");
                    break;
            }
        });
        this.click("#user [var=username]",e=>{
            if($("#user").toggleClass("logout"));
        });
    }
    /**
     * login button clicked (if form is showing submit login, otherwise show form)
     */
    login(){
        if($("#user").attr("login") == "false"){
            $("#user").attr("login","true");
        } else {
            var username = $("#user input[var=username]").val();
            var password = $("#user input[var=password]").val();
            this.view.model.login(username,password,json=>{
                this.view.display();
            });
        }
    }
    /**
     * signup button clicked (if form is showing submit signup, otherwise show form)
     */
    signup(){
        if($("#user").attr("login") == "false"){
            $("#user").attr("login","true");
        } else {
            var username = $("#user input[var=username]").val();
            var password = $("#user input[var=password]").val();
            this.view.model.signup(username,password,json=>{
                this.view.display();
            });
        }
    }
    /**
     * in case anything needs to check the user level in the UI
     * @param {function(Number)} callBack returns the user level
     */
    static userLevel(callBack){
        if(UserController.user.view.model){
            UserController.user.view.model.getData(json=>{
                callBack(json.session.user.level);
            });
        }
    }
    /**
     * in case anything needs to check the user level in the UI
     * @param {function(Number)} callBack returns the user level
     */
    static userId(callBack){
        if(UserController.user.view.model){
            UserController.user.view.model.getData(json=>{
                callBack(json.session.user.id);
            });
        }
    }
}
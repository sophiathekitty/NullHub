class UserController extends Controller {
    static user = new UserController();
    constructor(){
        super(new UserView());
        this.first_ready = true;
    }
    ready(){
        if(this.first_ready){
            this.view.build();
            this.setupUserEvents();
            this.first_ready = false;    
        }
    }
    setupUserEvents(){
        this.click("#user a",e=>{
            console.log($(e.currentTarget).attr("action"));
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
    static userLevel(callBack){
        if(UserController.user.view.model){
            UserController.user.view.model.getData(json=>{
                callBack(json.session.user.level);
            });
        }
    }
}
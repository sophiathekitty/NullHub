class UserController extends Controller {
    constructor(){
        super(new UserView());
    }
    ready(){
        this.view.build();
        this.setupUserEvents();
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
}
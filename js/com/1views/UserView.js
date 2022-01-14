/**
 * handles displaying the clock data
 */
class UserView extends View {
    constructor(){
        super(new UserModel(),new Template("user","/templates/stamps/user.html"));
    }
    /**
     * add the user view to the header
     */
    build(){
        if(this.template){
            this.template.getData(html=>{
                $(html).appendTo("header");
                this.display();
            })
        }
    }
    /**
     * populate user data
     */
    display(){
        if(this.model){
            this.model.getData(json=>{
                $("#user").attr("user_id",json.session.user_id);
                $("#user").attr("level",json.session.user.level);
                $("#user").attr("verified",json.session.user.verified);
                $("#user [var=username]").html(json.session.user.username);
                if(json.session.user.level == 5){
                    $("body").attr("dev","debug");
                }
                UserModel.verified = json.session.user.verified;
                UserModel.level= json.session.user.level;
            });
        }
    }
    /**
     * acting as an alias for display()
     */
    refresh(){
        this.display();
    }
}
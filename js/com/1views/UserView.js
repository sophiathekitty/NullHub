/**
 * handles displaying the clock data
 */
class UserView extends View {
    constructor(){
        super(new UserModel(),new Template("user","/templates/stamps/user.html"));
    }
    build(){
        if(this.template){
            this.template.getData(html=>{
                $(html).appendTo("header");
                this.display();
            })
        }
    }
    display(){

    }
    refresh(){
        this.display();
    }
}
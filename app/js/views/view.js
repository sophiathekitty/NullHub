class View {
    constructor(model,template){
        this.model = model;
        this.template = template;
    }
    display(){
        //throw "You need to extend display function to display view"
        this.model.getData(data=>{
            if(typeof(this.model) == Model){
                if(data && data[this.model.name]){
                    if(this.model.debug) console.log(data[this.model.name]);
                    Object.keys(itm).forEach(key=>{
                        console.log(key,data[this.model.name][key]);
                        if($("var=["+key+"]").hasClass("icon")){
                            $("var=["+key+"]").attr("val",data[this.model.name][key]);
                        } else {
                            $("var=["+key+"]").html(data[this.model.name][key]);
                        }
                    });
                }    
            }

        });
    }
    build(){
        throw "You need to extend display function to display view"
    }
}

class View {
    constructor(model,template = null, item_template=null){
        this.model = model;
        this.template = template;
        this.item_template = item_template;
        /*
        if(this.model instanceof Collection){
            console.log("view's model is collection");
        }
        */
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
        //throw "You need to extend display function to display view"
        this.model.getData(data=>{
            console.log(data);
            if(this.model instanceof Collection){
                // build collection list view
            }    
        });
    }
}

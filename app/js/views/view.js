class View {
    constructor(model,template = null, item_template = null){
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
        if(this.model instanceof Array){
            // multi model view
        } else {
            this.model.getData(data=>{
                if(this.model instanceof Collection){
                    console.log("display collection view",this.model.name);
    
                } else if(this.model instanceof Model){
                    // build model
                    console.log("display model view",this.model.name);
                }    
            });    
        }
    }
    build(){
        if(this.model instanceof Array){
            // build multiple models
            console.log("build view multi model");
            this.model.forEach(model=>{
                // build collection 
                console.log("build view multi model ::",model);
                model.getData(data=>{
                    console.log("build view multi model :: collection ::",data);
                    if(data != null){
                        if(model instanceof Collection){
                            this.item_template.getData(html=>{
                                data[model.name].forEach((itm,index)=>{
                                    $(html).appendTo("#"+model.name).attr('index',index);
                                    if('id' in itm) $("#"+model.name+" ."+model.item_name+"[index="+index+"]").attr(model.item_name+"_id",itm.id);
                                    this.populate("#"+model.name+" ."+model.item_name+"[index="+index+"]",itm);
                                });
    
                            },true);            
                        } else if(model instanceof Model){

                        }
                    }
                },true);
            });
        } else {
            console.log("build view?");
            this.model.getData(data=>{
                if(data != null){
                    console.log("build view:",data);
                    if(this.model instanceof Collection){
                        // build collection list view
                        console.log("build collection view",this.model.name,this.model.item_name);
                        this.item_template.getData(html=>{
                            console.log("build collection view item template loaded....",data,html);
                            data[this.model.name].forEach((itm,index)=>{
                                $(html).appendTo("#"+this.model.name).attr('index',index);
                                if('id' in itm) $("#"+this.model.name+" ."+this.model.item_name+"[index="+index+"]").attr(this.model.item_name+"_id",itm.id);
                                this.populate("#"+this.model.name+" ."+this.model.item_name+"[index="+index+"]",itm);
                            });
                        },true);
                    } else if(this.model instanceof Model){
                        // build model
                        console.log("build model view",this.model.name);
                    }    
                }
            },true);    
        }
    }
    populate(selector,itm){
        Object.keys(itm).forEach(key=>{
            //console.log(key,room[key]);
            if($(selector)[0].hasAttribute(key)){
                $(selector).attr(key,itm[key]);
            }
            if($(selector+" [var="+key+"]").length > 0){
                if($(selector+" [var="+key+"]")[0].hasAttribute(key)){
                    $(selector+" [var="+key+"]").attr(key,itm[key]);
                }
                if($(selector+" [var="+key+"]")[0].hasAttribute("pallet_value")){
                    $(selector+" [var="+key+"]").attr("pallet_value",Math.round(itm[key]));
                }            
                if($(selector+" [var="+key+"]").hasClass('bool') || $(selector+" [var="+key+"]")[0].hasAttribute("val")){
                    $(selector+" [var="+key+"]").attr("val",itm[key]);
                } else {
                    $(selector+" [var="+key+"]").html(itm[key]);
                }    
            }
        });
    }
}

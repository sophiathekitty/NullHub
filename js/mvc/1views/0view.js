class View {
    static refresh_ratio = 1;
    constructor(model,template = null, item_template = null, refresh_rate = 5000){
        this.model = model;
        this.template = template;
        this.item_template = item_template;
        this.refresh_rate = refresh_rate
        /*
        if(this.model instanceof Collection){
            console.log("view's model is collection");
        }
        */
        setTimeout(this.display,refresh_rate);
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
                    $("#"+this.model.name).addClass("loading");
                    this.model.getData(data=>{
                        $("#"+this.model.name).removeClass("loading");
                        console.log("display data",data);
                        this.populate("#"+this.model.name,data[this.model.name]);
                    });
                }    
            });    
        }
        setTimeout(this.display,this.refresh_rate*View.refresh_ratio);
    }
    build(){
        if(this.model instanceof Array){
            // build multiple models
            console.log("build view multi model");
            this.model.forEach(model=>{
                // build collection 
                console.log("build view multi model ::",model);
                $("#"+this.model.name).addClass("loading");
                model.getData(data=>{
                    $("#"+this.model.name).removeClass("loading");
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
            $("#"+this.model.name).addClass("loading");
            this.model.getData(data=>{
                $("#"+this.model.name).removeClass("loading");
                if(data != null){
                    console.log("build view:",data);
                    if(this.model instanceof Collection){
                        // build collection list view
                        console.log("build collection view",this.model.name,this.model.item_name);
                        $("#"+this.model.name).html("");
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
        //console.log("View Populate",selector,itm);
        Object.keys(itm).forEach(key=>{
            if(key != "hour"){
                //console.log(key,room[key]);
                var val = itm[key];
                if(val instanceof Array){
                    val = val[0];
                }
                //console.log(key,val);
                if($(selector)[0] && $(selector)[0].hasAttribute(key)){
                    $(selector).attr(key,val);
                }
                if($(selector+" [var="+key+"]").length > 0){
                    if($(selector+" [var="+key+"]")[0].hasAttribute(key)){
                        $(selector+" [var="+key+"]").attr(key,val);
                    }
                    if($(selector+" [var="+key+"]")[0].hasAttribute("pallet_value")){
                        $(selector+" [var="+key+"]").attr("pallet_value",val);
                    }            
                    if($(selector+" [var="+key+"]").hasClass('bool') || $(selector+" [var="+key+"]")[0].hasAttribute("val")){
                        $(selector+" [var="+key+"]").attr("val",val);
                    } else {
                        if($(selector+" [var="+key+"]").hasClass('date')){
                            var date = new Date(val);
                            var txt = date.getFullYear() + "-";
                            if(date.getMonth() < 10){
                                txt += "0";
                            }
                            txt += date.getMonth() + "-";
                            if(date.getDay() < 10){
                                txt += "0";
                            }
                            txt += date.getDay();
                            $(selector+" [var="+key+"]").html(txt);
                        } else {
                            $(selector+" [var="+key+"]").html(val);
                        }
                    }    
                }
                if($(selector+" [link="+key+"]").length > 0){
                    $(selector+" [link="+key+"]").attr("href",val);
                }
                if($(selector+" [ip="+key+"]").length > 0){
                    $(selector+" [ip="+key+"]").attr("href","http://"+val+"/");
                }
            }
        });
    }
}

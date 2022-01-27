/**
 * View
 * handles the main functions of building and displaying the interface elements 
 */
class View {
    static refresh_ratio = 1;
    /**
     * Create a view
     * @param {Model|Collection|HourlyChart|Model[]} model The data loading object. can be an array of Models
     * @param {Template} template The template loader for the main element (used for loading a single Model)
     * @param {Template} item_template The template loader for the items of a Collection
     * @param {Number} refresh_rate // how long in milliseconds to wait before refreshing. (is multiplied by View.refresh_ratio)
     */
    constructor(model,template = null, item_template = null, refresh_rate = 60000, debug = false){
        //console.log("ViewConstructor",model,template,item_template,refresh_rate);
        this.model = model;
        this.template = template;
        this.item_template = item_template;
        this.refresh_rate = refresh_rate
        this.debug = debug;
        
        //console.log("ViewConstructorVerify",this.model,this.template,this.item_template,this.refresh_rate);

        /*
        if(this.model instanceof Collection){
            console.log("view's model is collection");
        }
        */
        //setTimeout(this.refresh.bind(this),this.refresh_rate);
    }
    /**
     * Refresh the view with a dynamic refresh rate based on View.refresh_ratio
     * use bind to pass the instance to this function
     * example: this.refresh.bind(this)
     */
    refresh(){
        //console.log("refresh view",this.model.name);
        //console.log("refresh ratio",View.refresh_ratio,this.refresh_rate*View.refresh_ratio);
        this.display();
        //setTimeout(this.refresh.bind(this),this.refresh_rate*View.refresh_ratio);
        View.refresh_ratio += 0.01;
    }
    /**
     * Just populates the data doesn't build any elements. (unless it needs to just call the build)
     */
    display(){
        if(this.model){
            //throw "You need to extend display function to display view"
            if(this.model instanceof Array){
                // multi model view
                this.model.forEach(model=>{
                    model.getData(data=>{
                        //console.log("View::Display() model:",model," | data:",data);
                        if(model instanceof HourlyChart){
                            // redraw hourly chart?
                            if(this.debug) console.warn("display hourly charts not implemented yet!");
                        } else if(model instanceof Collection){
                            if($("#"+model.name+" ."+model.item_name).length != data[model.name].length){
                                if(this.debug) console.log("rebuilding?",$("#"+model.name+" ."+model.item_name).length,data[model.name].length);
                                this.build();
                            } else {
                                // just cycle through all the items and populate...
                                data[model.name].forEach((itm,index)=>{
                                    //console.log("just refresh item data","#"+this.model.name+" ."+this.model.item_name+" ["+this.model.item_name+"_id="+itm[this.model.id_name]+"]");
                                    this.populate("#"+model.name+" ."+model.item_name+"[index="+index+"]",itm);
                                });
                            }
                        } else if(model instanceof Model){
                            $("#"+this.model.name).addClass("loading");
                            model.getData(data=>{
                                $("#"+model.name).removeClass("loading");
                                //console.log("display data",data);
                                this.populate("#"+model.name,data[model.name]);
                            });
                        }
                    });
                })
            } else {
                this.model.getData(data=>{
                    if(this.model instanceof Collection){
                        if(this.debug) console.log("display collection view",this.model.name,this.model);
                        if($("#"+this.model.name+" ."+this.model.item_name).length != data[this.model.name].length){
                            if(this.debug) console.log("rebuilding?",$("#"+this.model.name+" ."+this.model.item_name).length,data[this.model.name].length);
                            this.build();
                        } else {
                            // just cycle through all the items and populate...
                            data[this.model.name].forEach((itm,index)=>{
                                //console.log("just refresh item data","#"+this.model.name+" ."+this.model.item_name+" ["+this.model.item_name+"_id="+itm[this.model.id_name]+"]");
                                this.populate("#"+this.model.name+" ."+this.model.item_name+"[index="+index+"]",itm);
                            });
                        }
                    } else if(this.model instanceof Model){
                        // build model
                        //console.log("display model view",this.model.name);
                        $("#"+this.model.name).addClass("loading");
                        this.model.getData(data=>{
                            $("#"+this.model.name).removeClass("loading");
                            //console.log("display data",data);
                            this.populate("#"+this.model.name,data[this.model.name]);
                        });
                    }    
                });    
            }
        } else {
            if(this.debug) console.error("View::Display >> view missing model?");
        }
    }
    /**
     * Builds the view
     */
    build(){
        if(this.debug) console.log("View::build----"+this.model.name);
        if(this.model instanceof Array){
            // build multiple models
            //console.log("build view multi model");
            this.model.forEach(model=>{
                // build collection 
                //console.log("build view multi model ::",model);
                $("#"+this.model.name).addClass("loading");
                model.getData(data=>{
                    $("#"+this.model.name).removeClass("loading");
                    //console.log("build view multi model :: collection ::",data);
                    if(data != null){
                        if(model instanceof HourlyChart){
                            if(this.debug) console.log("build an hourly chart? probably just chart populate....",model.name,model.chart_name,model.item_name,data['ranges']);
                            if('ranges' in data){
                                this.mappers = {};
                                Object.keys(data.ranges).forEach(key=>{
                                    //var max_temp_mapper = createRemap(min_temp,max_temp, 100,0);
                                    //var min_temp_mapper = createRemap(max_temp,min_temp, 100,0);
                                    this.mappers[key] = new ReMapper(data.ranges[key].min,data.ranges[key].max);
                                    //var min_mapper = this.createRemap(data.ranges[key].max,data.ranges[key].min, 100,0);
                                });
                                if(this.debug) console.log("hourly mappers",this.mappers);
                            }
                            data[model.item_name].forEach(hour=>{
                                var selector = "#"+model.chart_name+" [hour="+hour[model.id_name]+"]";
                                //console.log("hourly selector",selector,model.name);
                                this.populate(selector,hour,model.name);
                                this.drawChartBar(selector+" .bar.graph",hour,model.name);
                            });
                            $(".bar.graph div").html("");
                            // do sunrise stuff
                            var offset = 0;
                            Settings.loadVar('sunrise_time',sunrise_time=>{
                                var sunrise_date = new Date(sunrise_time*1000);
                                $("#"+model.chart_name).get(0).style.setProperty("--sunrise_start",this.DateToDayPercent(sunrise_date,offset-2));
                                $("#"+model.chart_name).get(0).style.setProperty("--sunrise",this.DateToDayPercent(sunrise_date,offset));
                                $("#"+model.chart_name).get(0).style.setProperty("--sunrise_end",this.DateToDayPercent(sunrise_date,offset+2));
                            });
                            Settings.loadVar('sunset_time',sunset_time=>{
                                var sunset_date = new Date(sunset_time*1000);
                                $("#"+model.chart_name).get(0).style.setProperty("--sunset_start",this.DateToDayPercent(sunset_date,offset-2));
                                $("#"+model.chart_name).get(0).style.setProperty("--sunset",this.DateToDayPercent(sunset_date,offset));
                                $("#"+model.chart_name).get(0).style.setProperty("--sunset_end",this.DateToDayPercent(sunset_date,offset+2));
                            });
                            //this.populate("#"+model.chart_name+" ",)
                        } else if(model instanceof Collection){
                            if(this.item_template){
                                this.item_template.getData(html=>{
                                    data[model.name].forEach((itm,index)=>{
                                        $(html).appendTo("#"+model.name).attr('index',index);
                                        if('id' in itm) $("#"+model.name+" ."+model.item_name+"[index="+index+"]").attr(model.item_name+"_id",itm[model.id_name]);
                                        this.populate("#"+model.name+" ."+model.item_name+"[index="+index+"]",itm);
                                    });
                                },true);    
                            } else {
                                data[model.name].forEach((itm,index)=>{
                                    if('id' in itm) $("#"+model.name+" ."+model.item_name+"[index="+index+"]").attr(model.item_name+"_id",itm[model.id_name]);
                                    //this.populate("#"+model.name+" ."+model.item_name+"[index="+index+"]",itm);
                                    this.populate("#"+model.name+" ."+model.item_name+"["+model.item_name+"_id="+itm[model.id_name]+"]",itm);
                                });
                            }
                        } else if(model instanceof Model){
                            if(this.template){
                                this.template.getData(html=>{
                                    // inject the template where it should go?
                                });
                            }
                        }
                    }
                },true);
            });
        } else {
            //console.log("build view?");
            $("#"+this.model.name).addClass("loading");
            this.model.getData(data=>{
                $("#"+this.model.name).removeClass("loading");
                if(data != null){
                    //console.log("build view:",data);
                    if(this.model instanceof HourlyChart){
                        if(this.debug) console.log("build hourly chart",this.model.name);
                    } else if(this.model instanceof Collection){
                        // build collection list view
                        //console.log("build collection view",this.model.name,this.model.item_name);
                        $("#"+this.model.name).html("");
                        this.item_template.getData(html=>{
                            //console.log("build collection view item template loaded....",this.model.name,data,html);
                            if(this.debug) console.log(this.model.name, data, data[this.model.name]);
                            data[this.model.name].forEach((itm,index)=>{
                                $(html).appendTo("#"+this.model.name).attr('index',index);
                                if('id' in itm) $("#"+this.model.name+" ."+this.model.item_name+"[index="+index+"]").attr(this.model.item_name+"_id",itm[this.model.id_name]);
                                this.populate("#"+this.model.name+" ."+this.model.item_name+"[index="+index+"]",itm);
                            });
                        },true);
                    } else if(this.model instanceof Model){
                        // build model
                        //console.log("build model view",this.model.name);
                    }    
                }
            },true);    
        }
    }
    /**
     * Populate html elements with data
     * @param  {String}   selector    The root selector for the chart items
     * @param  {Object}   itm         The data item being applied
     * @param  {String}   model       (optional) The name of the model the item comes from
     */
    populate(selector,itm,model = null){
        var mdl = "";
        if(model) mdl = "[model="+model+"]";
        //console.log("View Populate",selector,itm);
        Object.keys(itm).forEach(key=>{
            if(key != "hour"){
                //console.log(key,room[key]);
                var val = itm[key];
                if(val instanceof Array){
                    val = val[0];
                }
                var num = Math.round(val);
                if(!isNaN(num)) val = num;
                //console.log("hourly type of",key,typeof(val));
                //console.log(key,val);
                if($(selector)[0] && $(selector)[0].hasAttribute(key)){
                    $(selector).attr(key,val);
                }
                if($(selector+" [var="+key+"]"+mdl).length > 0){
                    //console.log("hourly var count",$(selector+" [var="+key+"]"+mdl).length)
                    if($(selector+" [var="+key+"]"+mdl)[0].hasAttribute(key)){
                        $(selector+" [var="+key+"]"+mdl).attr(key,val);
                    }
                    if($(selector+" [var="+key+"]"+mdl)[0].hasAttribute("pallet_value")){
                        $(selector+" [var="+key+"]"+mdl).attr("pallet_value",val);
                    }            
                    if($(selector+" [var="+key+"]"+mdl).hasClass('bool') || $(selector+" [var="+key+"]"+mdl)[0].hasAttribute("val")){
                        //console.log("hourly",key,$(selector+" [var="+key+"]"+mdl).hasClass('bool'), $(selector+" [var="+key+"]"+mdl)[0].hasAttribute("val"));
                        $(selector+" [var="+key+"]"+mdl).attr("val",val);
                    } else {
                        if($(selector+" [var="+key+"]"+mdl).hasClass('date')){
                            var date = new Date(val);
                            var txt = date.getFullYear() + "-";
                            if(date.getMonth()+1 < 10){
                                txt += "0";
                            }
                            txt += date.getMonth()+1 + "-";
                            if(date.getDate() < 10){
                                txt += "0";
                            }
                            txt += date.getDate();
                            console.log("date:",date.toDateString(),"val:",val,"txt:",txt);
                            $(selector+" [var="+key+"]"+mdl).html(txt);
                        } else {
                            $(selector+" [var="+key+"]"+mdl).html(val);
                        }
                        if($(selector+" [var="+key+"]"+mdl)[0].hasAttribute('pallet_name') && $(selector+" [var="+key+"]"+mdl)[0].hasAttribute('pallet_color')){
                            //console.log("color pallet stuff... itm has color?",selector+" [var="+key+"]"+mdl);
                            
                            var pallet = ColorPallet.getPallet($(selector+" [var="+key+"]"+mdl).attr('pallet_name'));
                            //console.log("color pallet",pallet,selector+" [var="+key+"]"+mdl);
                            if($(selector+" [var="+key+"]"+mdl)[0].hasAttribute('pallet_lerp')){
                                //console.log("color pallet do lerp?",selector+" [var="+key+"]"+mdl);                                
                                pallet.getColorLerp($(selector+" [var="+key+"]"+mdl).attr('pallet_color'),itm[key],color=>{
                                    //console.log("color pallet do lerp?",color,selector+" [var="+key+"]"+mdl);
                                    $(selector+" [var="+key+"]"+mdl).css("color",color);
                                });
                                
                            } else {
                                pallet.getColor($(selector+" [var="+key+"]"+mdl).attr('pallet_color'),color=>{
                                    //console.log("drawChartBar Color?",color);
                                    $(selector+" [var="+key+"]"+mdl).css("color",color);
                                });    
                            }
                        }
                    }    
                }
                if($(selector+" [link="+key+"]"+mdl).length > 0){
                    $(selector+" [link="+key+"]"+mdl).attr("href",val);
                }
                if($(selector+" [ip="+key+"]"+mdl).length > 0){
                    $(selector+" [ip="+key+"]"+mdl).attr("href","http://"+val+"/");
                }
            }
        });
    }
    /**
     * Applies styles to the div for a bar of an hourly chart
     * @param  {String}   selector    The root selector for the chart items
     * @param  {Object}   itm         The data item being applied
     * @param  {String}   model       The name of the model the item comes from
     */
    drawChartBar(selector,itm,model){
        var mdl = "";
        if(model) mdl = "[model="+model+"]";
        Object.keys(itm).forEach(key=>{
            if($(selector+" [var="+key+"]"+mdl).length > 0){
                //console.log("hourly how many did i find?",$(selector+" [var="+key+"]"+mdl).length);
                if($(selector+" [var="+key+"]"+mdl)[0].hasAttribute('var_min')){
                    var min_key = $(selector+" [var="+key+"]"+mdl).attr("var_min");
                    var max_key = $(selector+" [var="+key+"]"+mdl).attr("var_max");
                    var min = itm[min_key];
                    var max = itm[max_key];
                    //console.log("hourly bar chart item var found?",selector+" [var="+key+"]"+mdl,min,itm[key],max);
                    if(this.mappers[key]){
                        $(selector+" [var="+key+"]"+mdl).css("top",this.mappers[key].max_mapper(max)+"%");
                        $(selector+" [var="+key+"]"+mdl).css("bottom",this.mappers[key].min_mapper(min)+"%");
                    } else {
                        //console.error("hourly chart missing mapper",key);
                    }
                    if($(selector+" [var="+key+"]"+mdl)[0].hasAttribute('pallet_name') && $(selector+" [var="+key+"]"+mdl)[0].hasAttribute('pallet_color')){
                        var pallet = ColorPallet.getPallet($(selector+" [var="+key+"]"+mdl).attr('pallet_name'));
                        if($(selector+" [var="+key+"]"+mdl)[0].hasAttribute('pallet_lerp')){
                            pallet.getColorLerp($(selector+" [var="+key+"]"+mdl).attr('pallet_color'),itm[key],color=>{
                                //console.log("drawChartBar Color?",color);
                                $(selector+" [var="+key+"]"+mdl).css("background-color",color);
                            });    
                        } else {
                            pallet.getColor($(selector+" [var="+key+"]"+mdl).attr('pallet_color'),color=>{
                                //console.log("drawChartBar Color?",color);
                                $(selector+" [var="+key+"]"+mdl).css("background-color",color);
                            });    
                        }
                    }
                }
            }
        });
    }
    /**
     * Maps a time of day to the percentage of the day
     * @param  {Date}     date    The time of day
     * @param  {Number}   offset  The hours offset for time of day
     * @return {Number}           The percentage of the day the time is
     */
    DateToDayPercent(date,offset = 0){
        var hours = date.getHours();
        var min = date.getMinutes()
        return (((((hours+offset)*60) + min) / (24*60))*100)+"%";
    }
    DateTimeToTimeString(time){
        var dt = time.split(" ");
        return this.Time24to12(dt[1])+this.Time24toAM(dt[1]);
    }
    /**
     * convert 13:23 to 1:23pm
     * @param {string} time 
     */
    Time24to12(time){
        var t = time.split(":");
        var h = Number(t[0]);
        var m = t[1];
        if(h > 12){
            h -= 12;
        }
        if(h == 0) h = 12;
        return h+":"+m;
    }
    Time24toAM(time){
        var am = "am";
        var t = time.split(":");
        var h = Number(t[0]);
        if(h >= 12) am = "pm";
        return am;
    }
}
/**
 * ReMapper
 * creates the min and max mappers for displaying hourly bar graphs
 */
class ReMapper {
    /**
     * Creates the min and max mappers for displaying hourly bar graphs
     * @param {Number} min       The min input
     * @param {Number} max       The max input
     * @param {Number} range_max The max output
     * @param {Number} range_min The min output
     */
    constructor(min,max,range_max = 100, range_min = 0){
        this.max_mapper = this.createRemap(min,max, range_max,range_min);
        this.min_mapper = this.createRemap(max,min, range_max,range_min);
    }
    /**
     * Create a function that maps a value to a range
     * @param  {Number}   inMin    Input range minimum value
     * @param  {Number}   inMax    Input range maximun value
     * @param  {Number}   outMin   Output range minimum value
     * @param  {Number}   outMax   Output range maximun value
     * @return {function}          A function that converts a value
     * 
     * @author Victor N. wwww.victorborges.com
     * @see https://gist.github.com/victornpb/51b0c17241ea483dee2c3a20d0f710eb/
     */
    createRemap(inMin, inMax, outMin, outMax) {
        return function remaper(x) {
            return (x - inMin) * (outMax - outMin) / (inMax - inMin) + outMin;
        };
    }

}
$(document).mousemove(function(e){
    View.refresh_ratio = 1;
});
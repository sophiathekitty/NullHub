class AppView extends View{
    constructor(){
        super(new TemplateSections())
        this.sections = Array();
    }
    display(){
        
    }
    build(){
        //console.log("build!");
        this.model.getData(data=>{
            //console.log("build?");
            if(data != null){
                // build sections
                data[this.model.name].forEach(section=>{
                    // single api section
                    if('name' in section){
                        //console.log("section name: ",section['name']);
                        var template = new Template(section['template_name'],section['template']);
                        var model;
                        var itm_template;
                        if(section['type'] == "model"){
                            model = new Model(section['item_name'],section['api'],section['api'],1000*60*5);
                        }
                        if(section['type'] == "collection"){
                            model = new Collection(section['name'],section['item_name'],section['api'],section['api'],1000*60*5);
                            itm_template = new Template(section['item_name'],section['item_template']);
                        }
                        if(section['type'] == "hourly_chart"){
                            console.log("hourly section?");
                            //model = new Collection(section['name'],section['item_name'],section['api'],section['api'],1000*60*5);
                            //itm_template = new Template(section['item_name'],section['item_template']);
                        }
                        var sectionCon = new SectionController(section['name'],model,template,itm_template);
                        template.getData(data=>{
                            //console.log("template data",data);
                            //console.log($("#sections #"+section['name']).length);
                            if($("#sections #"+section['name']).length == 0) $("#sections").append(data);
                            sectionCon.view.build();
                            sectionCon.basicEvents();
                        },true);
                        //console.log(sectionCon);
                        this.sections.push(sectionCon);
                    }
                    // multi api section
                    if('models' in section){
                        console.log(section['models']);
                        var template = new Template(section['template_name'],section['template']);
                        var model = new Array();
                        var item_template;
                        var name = "";
                        section.models.forEach(api=>{
                            console.log("hourly? maybe?",api);
                            if(api.type == "collection"){
                                if(name == "") name = api['name'];
                                model.push(new Collection(api['name'],api['item_name'],api['api'],api['api'],1000*60*5));
                                if('item_template' in api) item_template = new Template(api['item_name'],api['item_template']);
                            }
                            if(api.type == "hourly_chart"){
                                console.log("the other hourly section build thing?");
                                model.push(new HourlyChart(api['name'],api['item_name'],api['chart_name'],api['api']))
                            }
                        });
                        var sectionCon = new SectionController(section['template_name'],model,template,item_template);
                        template.getData(data=>{
                            //console.log("template data",data);
                            if($("#sections #"+section['template_name']).length == 0) $("#sections").append(data);
                            sectionCon.view.build();
                            // bind section events
                            sectionCon.basicEvents();
                        },true);
                    }
                    $("nav.sections").append("<a href=\"#"+section['template_name']+"\">"+section['template_name']+"</a>");
                });
            }
        },true);

    }
}
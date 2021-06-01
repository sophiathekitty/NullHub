class AppView extends View{
    constructor(){
        super(new TemplateSections())
        this.sections = Array();
    }
    build(){
        console.log("build!");
        this.model.getData(data=>{
            console.log("build?");
            if(data != null){
                // build sections
                data[this.model.name].forEach(section=>{
                    if('name' in section){
                        console.log("section name: ",section['name']);
                        var template = new Template(section['name'],section['template']);
                        var model;
                        var itm_template;
                        if(section['type'] == "model")
                            model = new Model(section['item_name'],section['api'],section['api'],1000*60*5);
                        if(section['type'] == "collection"){
                            model = new Collection(section['name'],section['item_name'],section['api'],section['api'],1000*60*5);
                            itm_template = new Template(section['item_name'],section['item_template']);
                        }
                        var sectionCon = new SectionController(model,template,itm_template);
                        template.getData(data=>{
                            //console.log("template data",data);
                            console.log($("#sections #"+section['name']).length);
                            if($("#sections #"+section['name']).length == 0) $("#sections").append(data);
                            sectionCon.view.build();
                        },true);
                        console.log(sectionCon);
                        this.sections.push(sectionCon);
                    }
                    if('models' in section){
                        //console.log(section['models']);
                    }
                });
            }
        },true);

    }
}
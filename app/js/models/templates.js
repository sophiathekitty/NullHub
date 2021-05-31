class Template extends Model{
    constructor(name,template){
        super(name,template,template,1000*60*5);
        this.prefix = "template_";
    }
}
class Templates extends Collection {
    constructor(collection_name = 'templates',item_name = 'template',get_url = '/api/templates',save_url = '/api/templates',id_name = "template"){
        super(collection_name,item_name,get_url,save_url,id_name);
    }
}
class TemplateSections extends Templates {
    constructor(){
        super('sections','section','/api/templates/sections');
    }
}
//var sections = new TemplateSections();
//sections.loadSections();
/**
 * a model setup for loading html templates to be used by views
 */
class Template extends Model{
    /**
     * the constructor for a new Template("example","/templates/example.html")
     * @param {string} name the name of the template (used for localStorage name i think?)
     * @param {string} template the url of the template to load
     */
    constructor(name,template){
        super(name,template,template,1000*60*5);
        this.prefix = "template_";
    }
}
/**
 * a collection of templates... looks like it will default to just loading in all the templates?
 */
class Templates extends Collection {
    /**
     * 
     * @param {string} collection_name name of the templates collection
     * @param {string} item_name name of an item (not really sure where this is ever used?)
     * @param {string} get_url the url to load the template
     * @param {string} save_url not really needed... no saving templates lol
     * @param {string} id_name the id field name... (ie: item.id, item.hour)
     */
    constructor(collection_name = 'templates',item_name = 'template',get_url = '/api/templates',save_url = '/api/templates',id_name = "template"){
        super(collection_name,item_name,get_url,save_url,id_name);
    }
}
/**
 * this looks like it handles loading all the templates for sections?
 */
class TemplateSections extends Templates {
    constructor(){
        super('sections','section','/api/templates/sections');
    }
}
//var sections = new TemplateSections();
//sections.loadSections();
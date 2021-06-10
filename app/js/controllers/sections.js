class SectionController extends Controller {
    /**
     * Create a section controller
     * @param {Model|Collection|HourlyChart|Model[]} model The data loading object. can be an array of Models
     * @param {Template} template The template loader for the main element (used for loading a single Model)
     * @param {Template} item_template The template loader for the items of a Collection
     */
    constructor(name,model,template,item_template){
        super(new View(model,template,item_template));
        this.name = name;
    }
    basicEvents(){
        console.log("basic events","#"+this.name+" nav.filter a",$("#"+this.name+" nav a").length);
        var name = this.name;
        this.click("#"+this.name+" nav.filters a",e=>{
            e.preventDefault();
            console.log("click events filter tab",$(e.currentTarget).attr("filter"));
            $("#"+name).attr("show",$(e.currentTarget).attr("filter"));
        });
        this.click("#"+this.name+" nav.focus a",e=>{
            e.preventDefault();
            console.log("click events filter tab",$(e.currentTarget).attr("focus"));
            $("#"+name).attr("focus",$(e.currentTarget).attr("focus"));
        });        
    }
}
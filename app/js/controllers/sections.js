class SectionController extends Controller {
    /**
     * Create a section controller
     * @param {Model|Collection|HourlyChart|Model[]} model The data loading object. can be an array of Models
     * @param {Template} template The template loader for the main element (used for loading a single Model)
     * @param {Template} item_template The template loader for the items of a Collection
     */
    constructor(model,template,item_template){
        super(new View(model,template,item_template));
    }
}
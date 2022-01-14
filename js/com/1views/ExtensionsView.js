/**
 * view definition for extensions (doesn't override display or build)
 */
class ExtensionsView extends View {
    constructor(){
        super(new ExtensionsCollection(),null,new Template("extension","/templates/items/extension.html"));
    }
}
/**
 * load list of extension info
 */
class ExtensionsCollection extends Collection {
    constructor(){
        super("extensions","extension","/api/info/extensions/","/api/info/extensions/")
    }
}
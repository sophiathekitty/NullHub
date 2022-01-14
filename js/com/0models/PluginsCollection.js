/**
 * loads a list of plugin info
 */
class PluginsCollection extends Collection {
    constructor(){
        super("plugins","plugin","/api/info/plugins/","/api/info/plugins/")
    }
}
/**
 * view definition for plugins (doesn't override display or build)
 */
class PluginsView extends View {
    constructor(){
        super(new PluginsCollection(),null,new Template("plugins","/templates/items/plugin.html"));
    }
}
/**
 * view definition for plugins (doesn't override display or build)
 */
class PluginsView extends View {
    constructor(){
        super(new PluginsCollection(),null,new Template("plugins","/templates/items/plugin.html"));
    }
    build(){
        if(this.item_template && this.model){
            this.item_template.getData(html=>{
                this.model.getData(json=>{
                    json.plugins.forEach(plugin=>{
                        $(html).appendTo("#plugins").attr("plugin_id",plugin.id);
                    });
                    this.display();
                });
            });
        }
    }
    display(){
        if(this.model){
            this.model.getData(json=>{
                json.plugins.forEach(plugin=>{
                    $("#plugins [plugin_id="+plugin.id+"] [var=name]").html(plugin.name);
                    $("#plugins [plugin_id="+plugin.id+"] [link=git]").attr("href",plugin.git);
                    $("#plugins [plugin_id="+plugin.id+"] [link=local]").attr("href",plugin.local);
                    $("#plugins [plugin_id="+plugin.id+"] [var=hash]").html(plugin.hash.substr(0,7));
                });
            });
        }
    }
}
/**
 * view definition for extensions (doesn't override display or build)
 */
class ExtensionsView extends View {
    constructor(){
        super(new ExtensionsCollection(),null,new Template("extension","/templates/items/extension.html"));
    }
    build(){
        if(this.item_template && this.model){
            this.item_template.getData(html=>{
                this.model.getData(json=>{
                    json.extensions.forEach(extension=>{
                        $(html).appendTo("#extensions").attr("extension_id",extension.id);
                    });
                    this.display();
                });
            });
        }
    }
    display(){
        if(this.model){
            this.model.getData(json=>{
                json.extensions.forEach(extension=>{
                    $("#extensions [extension_id="+extension.id+"] [var=name]").html(extension.name);
                    $("#extensions [extension_id="+extension.id+"] [link=git]").attr("href",extension.git);
                    $("#extensions [extension_id="+extension.id+"] [link=path]").attr("href",extension.path);
                    $("#extensions [extension_id="+extension.id+"] [var=hash]").html(extension.hash.substr(0,7));
                });
            });
        }
    }
}
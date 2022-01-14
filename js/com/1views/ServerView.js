/**
 * server list view
 */
class ServerView extends View {
    constructor(){
        super(new Collection("servers","server","/api/info/servers/","/api/info/servers/","mac_address"),null,new Template("server","/templates/items/server.html"));
    }
    /**
     * build the server list
     */
    build(){
        if(this.item_template && this.model){
            this.item_template.getData(html=>{
                this.model.getData(json=>{
                    $("ul#servers").html("");
                    json.servers.forEach((server,index)=>{
                        $(html).appendTo("ul#servers").attr("index",index);
                        $("#servers [index="+index+"]").attr("server_id",server.mac_address);
                        $("#servers [index="+index+"]").attr("type",server.type);
                        $("#servers [index="+index+"]").attr("online",server.online);
                        if(server.offline > 0) $("#servers [index="+index+"]").attr("offline","1");
                        $("#servers [index="+index+"] a").attr("href","http://"+server.url+"/");
                        $("#servers [index="+index+"] [var=name]").html(server.name);
                    });
                });
            });
        }
    }
    /**
     * acting as an alias for build() (ie: rebuild every time....)
     */
    display(){
        this.build();
    }
}
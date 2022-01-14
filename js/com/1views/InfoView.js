/**
 * view definition for device info (doesn't override display or build)
 */
class InfoView extends View {
    constructor(){
        super(new InfoModel());
    }
    /**
     * alias for display
     */
    build(){
        this.display();
    }
    /**
     * display the info
     */
    display(){
        if(this.model){
            this.model.getData(json=>{
                $("#info [var=name]").html(json.info.name);
                $("#info [var=url]").html(json.info.url);
                $("#info [var=url]").attr("href","http://"+json.info.url+"/");
                $("#info [var=type]").html(json.info.type);
                $("#info [var=server]").html(json.info.server);
                $("#info [var=mac_address]").html(json.info.mac_address);
                $("#info [var=hash]").html(json.info.hash.substr(0,7));
                $("#info [var=main]").attr("href","http://"+json.info.git+"/commit/"+json.info.hash);
                $("#info [var=main]").attr("val",json.info.main);
                $("#info [var=hub_name]").html(json.info.hub_name);
                $("#info [var=hub_name]").attr("href","http://"+json.info.hub+"/");
            });
        }
    }
    /**
     * alias for display
     */
    refresh(){
        this.display();
    }
}
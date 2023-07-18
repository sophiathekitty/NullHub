/**
 * main menu view handler
 */
class ServicesView extends View {
    constructor(){
        super(null,new Template("main_menu","/widgets/services/services.php"),new Template("main_menu","/widgets/services/logs.php"),100000,true);
    }
    /**
     * build the menu view
     */
    build(){
        if(this.template){
            this.template.getData(html=>{
                $(html).appendTo("body");
                var dialog = document.getElementById('services-list');
                dialog.showModal();
                dialog.blur();
            });
        }
    }
    /**
     * show the main menu
     */
    show(){
        $("dialog#services-list").remove();
        this.build();
    }
    /**
     * close the main menu
     */
    hide(){
        $("dialog#services-list").remove();
    }
    /**
     * build the logs view
     * @param {string} name the service name
     */
    buildLogs(name){
        if(this.item_template){
            this.item_template.get_params = "?name="+name;
            if(this.debug) console.log("ServicesView::BuildLogs",name,this.item_template.get_params);
            this.item_template.getData(html=>{
                $(html).appendTo("body");
                var dialog = document.getElementById('service-logs-view');
                dialog.showModal();
                dialog.blur();
            });
        }
    }
    /**
     * load the service logs
     * @param {string} name the service name
     */
    showLogs(name){
        $("dialog#service-logs-view").remove();
        if(this.debug) console.log("ServicesView::ShowLogs",name);
        this.buildLogs(name);
    }
    /**
     * close the service logs
     */
    hideLogs(){
        $("dialog#service-logs-view").remove();
    }
    
}
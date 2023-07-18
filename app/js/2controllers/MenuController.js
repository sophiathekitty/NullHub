/**
 * menu controller
 */
class MenuController extends Controller {
    static instance = new MenuController();
    constructor(debug = true){
        if(debug) console.info("MenuController::Constructor");
        super(new MenuView(),debug);
        this.services = new ServicesView();
    }
    /**
     * setup menu event handling
     */
    ready(){
        if(this.debug) console.info("MenuController::Ready");
        
        /**
         * show menu
         */
        this.click("header","button.main_menu_btn",e=>{
            if(this.debug) console.debug("MenuController::header h1::Click");
            e.preventDefault();
            this.view.show();
        });

        /**
         * hide menu
         */
        this.click("body","dialog#main_menu h1",e=>{
            if(this.debug) console.debug("MenuController::dialog#main_menu h1::Click");
            e.preventDefault();
            this.view.hide();
        });

        /**
         * toggle bool value
         */
        this.click("body","dialog#main_menu span.value.bool",e=>{
            if(this.debug) console.debug("MenuController::dialog#main_menu span.value.bool::Click",$(e.currentTarget).attr("var"),$(e.currentTarget).attr("val"));
            e.preventDefault();
            var key = $(e.currentTarget).attr("var");
            var val = 0;
            if($(e.currentTarget).attr("val") == 0) val = 1;
            Settings.saveVar(key,val,json=>{
                if(this.debug) console.log("MenuController::Settings - Save Complete",key,json);
                $(e.currentTarget).attr("val",val);
            },e=>{
                if(this.debug) console.error("MenuController::Settings - Save Error",key,e);
            },e=>{
                if(this.debug) console.error("MenuController::Settings - Save Failed",key,e);
            });
            
        });        
        /**
         * show edit field
         */
        this.click("body","dialog#main_menu span.value.field",e=>{
            if(this.debug) console.debug("MenuController::dialog#main_menu span.value.field::Click",$(e.currentTarget).attr("var"));
            e.preventDefault();
            this.view.showEditField($(e.currentTarget).attr("var"));
        });        

        /**
         * save and close edit field
         */
        this.focusout("body","dialog#main_menu input.edit.field",e=>{
            if(this.debug) console.debug("MenuController::dialog#main_menu input.edit.field::FocusOut",$(e.currentTarget).attr("var"));
            e.preventDefault();
            var key = $(e.currentTarget).attr("var");
            Settings.saveVar(key,$(e.currentTarget).val(),e=>{
                if(this.debug) console.log("MenuController::Settings - Save Complete",key,e);
                this.view.hideEditField(key);
            },e=>{
                if(this.debug) console.error("MenuController::Settings - Save Error",key,e);
            },e=>{
                if(this.debug) console.error("MenuController::Settings - Save Failed",key,e);
            });
        });

        /**
         * show edit field
         */
        this.click("body","dialog#main_menu span.value.select",e=>{
            if(this.debug) console.debug("MenuController::dialog#main_menu span.value.select::Click",$(e.currentTarget).attr("var"));
            e.preventDefault();
            this.view.showSelect($(e.currentTarget).attr("var"));
        });        
        
        /**
         * save select change (settings)
         */
        this.change("body","dialog#main_menu select[model=settings]",e=>{
            if(this.debug) console.debug("MenuController::dialog#main_menu select[model=settings]::Change",$(e.currentTarget).attr("var"));
            e.preventDefault();
            var key = $(e.currentTarget).attr("var");
            Settings.saveVar(key,$(e.currentTarget).val(),e=>{
                if(this.debug) console.log("MenuController::Settings - Save Complete",key,e);
                this.view.hideSelect(key);
            },e=>{
                if(this.debug) console.error("MenuController::Settings - Save Error",key,e);
            },e=>{
                if(this.debug) console.error("MenuController::Settings - Save Failed",key,e);
            });
        });
        /**
         * save select change (settings)
         */
        this.change("body","dialog#main_menu select[collection=hub_candidates]",e=>{
            if(this.debug) console.debug("MenuController::dialog#main_menu select[collection=hub_candidates]::Change",$(e.currentTarget).attr("var"));
            e.preventDefault();
            var key = $(e.currentTarget).attr("var");
            
            ElectionsModel.appoint($(e.currentTarget).val(),e=>{
                if(this.debug) console.log("MenuController::Election - Save Complete",key,e);
                this.view.hideSelect(key);
            },e=>{
                if(this.debug) console.error("MenuController::Election - Save Error",key,e);
            },e=>{
                if(this.debug) console.error("MenuController::Election - Save Failed",key,e);
            });
            
        });
        /**
         * services sub menus
         */
        /**
         * show services list
         */
        this.click("body","dialog#main_menu a#show_services",e=>{
            if(this.debug) console.debug("MenuController::dialog#main_menu a#show_services::Click");
            e.preventDefault();
            this.services.show();
        });

        /**
         * hide services list
         */
        this.click("body","dialog#services-list h1",e=>{
            if(this.debug) console.debug("MenuController::dialog#services-list h1::Click");
            e.preventDefault();
            this.services.hide();
        });
        /**
         * show service logs
         */
        this.click("body","dialog#services-list li a",e=>{
            if(this.debug) console.debug("MenuController::dialog#dialog#services-list li a::Click",$(e.currentTarget).attr("service"));
            e.preventDefault();
            this.services.showLogs($(e.currentTarget).attr("service"));
        });

        /**
         * hide service logs
         */
        this.click("body","dialog#service-logs-view h1",e=>{
            if(this.debug) console.debug("MenuController::dialog#service-logs-view h1::Click");
            e.preventDefault();
            this.services.hideLogs();
        });

    }
}
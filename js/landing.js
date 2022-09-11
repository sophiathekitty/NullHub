var info = new InfoView();//View(new Model("info","/api/info/","/api/info/"));
var plugins = new PluginsView();//View(new Collection("plugins","plugin","/api/info/plugins/","/api/info/plugins/"),null,new Template("plugin","/templates/items/plugin.html"));
var extensions = new ExtensionsView();//View(new Collection("extensions","extension","/api/info/extensions/","/api/info/extensions/"),null,new Template("extension","/templates/items/extension.html"));
var servers = new ServerView();
var settings = new Settings();
var weather_pallet = ColorPallet.getPallet("weather");
weather_pallet.getColorLerp("temp",68,color=>{
    console.debug("color",color);
});
var clock = new ClockController();
//var user = new UserController();
//info.model.pull_delay = 60000;
$(document).ready(function(){
    console.debug("landing--document ready");
    // load the server info
    info.display();
    plugins.build();
    extensions.build();
    servers.build();
    //clock.ready();
    //user.ready();
    //window.refreshData = setInterval(RefreshData,10000);
    LoadReadMe();
});

// refreshes data lol
function RefreshData(){
    //console.log("refresh display");
    //info.model.pullData();
    info.display();
    plugins.display();
    extensions.display();
    servers.display();
}




var already_loaded = false;
var already_loaded_extensions = false;

function LoadReadMe(){
    settings.getVar("type",data=>{
        console.debug("load readme type",data);
        var md = "/DEVICE.md";
        if(data == "hub"){
            md = "/HUB.md";
        }
        settings.getVar("name",title=>{
            if(data == "hub"){
                $("title").html("Null [Hub] "+title);
            } else if(data == "" || data == "device") {
                $("title").html("Null [Device] "+title);
            } else {
                $("title").html("Null ["+data.charAt(0).toUpperCase()+data.slice(1)+"] "+title);
            }    
        });
        $("body").addClass(data);
        //md = "README.md";
        if(data == "setup"){
            // show setup wizard instead of about stuff.
            //console.error("setup mode... testing");
            var setup_wizard = new SetupController(true);
        } else $.get(md).done(json=>{
            //$("#about").html(markdown.toHTML(json));
            $("#about").html("<article class=\"about\"></article>");
            $("#about article.about").html(marked(json));
            plugins.model.getData(data=>{
                console.debug("load readme plugins",data);
                if(!already_loaded){
                    data.plugins.forEach(plugin=>{
                        $.get(plugin.local+"ABOUT.md").done(json=>{
                            $("#about article.about").append(marked(json));
                            $("#about a").attr("target","_blank");
                        });
                    });    
                }
                already_loaded = true;
            },true);
            extensions.model.getData(data=>{
                console.debug("load readme extensions",data);
                if(!already_loaded_extensions){
                    data.extensions.forEach(extension=>{
                        $.get(extension.path+"ABOUT.md").done(json=>{
                            $("#about").append("<article>"+marked(json)+"</article>");
                            $("#about a").attr("target","_blank");
                        });
                    });    
                }
                already_loaded_extensions = true;
            },true);
            $("#about a").attr("target","_blank");
        }).fail(e=>{
            console.log(e);
        });        
    });
}


/**
 * -----------------------------------------------------------------------------------------------------------
 * 
 * setup ui stuff
 * 
 * -----------------------------------------------------------------------------------------------------------
 */


/**
 * save settings
 */
class SetupModel extends Model {
    constructor(debug = false){
        if(debug) console.debug("SetupModel::Constructor");
        super("setup","/api/info/setup/","/api/info/setup/");
        this.debug = debug;
    }
    /**
     * 
     * @param {string} form selector for the form
     * @returns {json} json object from form
     */
    convertFormToJSON(form) {
        const array = $(form).serializeArray(); // Encodes the set of form elements as an array of names and values.
        const json = {};
        $.each(array, function () {
            json[this.name] = this.value || "";
        });
        return json;
    }
}

/**
 * view definition for device info (doesn't override display or build)
 */
class SetupView extends View {
    /**
     * constructor
     * @param {Controller} controller the controller
     * @param {bool} debug show console output
     */
    constructor(debug = false){
        if(debug) console.debug("SetupView::Constructor");
        super(new SetupModel(debug),new Template("setup","/templates/sections/setup.html",null,60000, debug));
        this.debug = debug;
    }
    /**
     * alias for display
     */
    build(){
        if(this.debug) console.debug("SetupView::Build");
        // load the setup template
        if(this.template){
            this.template.getData(html=>{
                $(html).appendTo("#about");
                if(this.controller) this.controller.ready();
                this.display();
            });
        }
    }
    /**
     * display the setup form
     */
    display(){
        if(this.debug) console.debug("SetupView::Display");
        if(this.model){
            this.model.getData(json=>{
                if(this.debug) console.log("SetupView::Display",json);
                if('defaults' in json) $("#setup_form #device_type").val(json.defaults.device_type);
                if('type' in json) $("#setup_form #device_type").val(json.type);
                if('url' in json) $("#setup_form #hub_url").val(json.url);
                if('name' in json) $("#setup_form #device_name").val(json.name);
            });
        }
    }
    /**
     * alias for display
     */
    refresh(){
        if(this.debug) console.warn("SetupView::Refresh (does nothing. why call?)");
        // don't do anything for refresh....
    }
}

/**
 * handles the slideshow and interaction with the clock widget
 */
class SetupController extends Controller {
    /**
     * constructor
     * @param {bool} debug show console output
     */
    constructor(debug = false){
        if(debug) console.debug("SetupController::Constructor");
        super(new SetupView(debug),debug);
        this.first_ready = true;
        this.view.controller = this;
        this.view.build();
    }
    /**
     * build the view and setup the slideshow timeout and manual progression
     */
    ready(){
        if(this.first_ready){
            if(this.debug) console.debug("SetupController::Ready");
            this.listenForEvent("submit","form#setup_form",e=>{
                e.preventDefault();
                var json = this.view.model.convertFormToJSON($(e.target));
                if(this.debug) console.info("SetupController::Submit",e,json);
                $("form [type=submit]").hide();
                ("#setup_status_message").html("attempting to create settings file and install database.... and will try to sync important data from main hub... this might take a short while.");
                this.view.model.setData(json);
                this.view.model.pushData(json =>{
                    if(this.debug) console.log("SetupController::Submit::Success",json);
                    if(json.setup == "complete"){
                        $("#setup_form").hide();
                        $("#setup_status_message").html("Success: "+json.setup);
                        if('install' in json) $("#setup_status_message").html("Success: "+json.install);
                    } else {
                        $("form [type=submit]").show();
                        $("#setup_status_message").html("Error: "+json.setup);
                        if('install' in json) $("#setup_status_message").html(json.install);
                    }                    
                    if('die' in json) $("#setup_status_message").html("Error: "+json.die);
                    if('die' in json) $("#setup_form").show();
                },error=>{
                    if(this.debug) console.error("SetupController::Submit::Error",error);
                    $("#setup_status_message").html("Error: "+error.responseText);
                    $("form [type=submit]").show();
                },fail=>{
                    if(this.debug) console.error("SetupController::Submit::Fail",fail);
                    $("#setup_status_message").html("Fail: "+fail.responseText);
                    $("form [type=submit]").show();
                });
            });
            this.first_ready = false;
        }
    }
}
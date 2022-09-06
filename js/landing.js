var info = new InfoView();//View(new Model("info","/api/info/","/api/info/"));
var plugins = new PluginsView();//View(new Collection("plugins","plugin","/api/info/plugins/","/api/info/plugins/"),null,new Template("plugin","/templates/items/plugin.html"));
var extensions = new ExtensionsView();//View(new Collection("extensions","extension","/api/info/extensions/","/api/info/extensions/"),null,new Template("extension","/templates/items/extension.html"));
var servers = new ServerView();
var settings = new Settings();
var weather_pallet = ColorPallet.getPallet("weather");
weather_pallet.getColorLerp("temp",68,color=>{
    console.log("color",color);
});
var clock = new ClockController();
//var user = new UserController();
//info.model.pull_delay = 60000;
$(document).ready(function(){
    console.log("landing--document ready");
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
        console.log("load readme type",data);
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
                console.log("load readme plugins",data);
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
                console.log("load readme extensions",data);
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
    constructor(){
        super("setup","/api/info/setup/","/api/info/setup/");
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
        super(new SetupModel(),new Template("setup","/templates/sections/setup.html",null,60000, debug));
    }
    /**
     * alias for display
     */
    build(){
        // load the setup template
        if(this.template){
            this.template.getData(html=>{
                $(html).appendTo("#about");
                if(this.controller) this.controller.ready();
            });
        }
    }
    /**
     * display the setup form
     */
    display(){
        if(this.model){
            this.model.getData(json=>{
                $("#setup_form #device_type").val(json.setup.device_type);
            });
        }
    }
    /**
     * alias for display
     */
    refresh(){
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
        if(debug) console.log("SetupController::Constructor");
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
            if(this.debug) console.log("SetupController::Ready");
            this.listenForEvent("submit","form#setup_form")
            this.click(".clock",e=>{
                //this.slideshow();
            });
            this.first_ready = false;
        }
    }
}
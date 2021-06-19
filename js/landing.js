var info = new View(new Model("info","/api/info/","/api/info/"));
var plugins = new View(new Collection("plugins","plugin","/api/info/plugins/","/api/info/plugins/"),null,new Template("plugin","/templates/items/plugin.html"));
var extensions = new View(new Collection("extensions","extension","/api/info/extensions/","/api/info/extensions/"),null,new Template("template","/templates/items/extension.html"));
var settings = new Settings();
var weather_pallet = ColorPallet.getPallet("weather");
weather_pallet.getColorLerp("temp",68,color=>{
    console.log("color",color);
});
//info.model.pull_delay = 60000;
$(document).ready(function(){
    // load the server info
    info.display();
    plugins.build();
    extensions.build();
    //window.refreshData = setInterval(RefreshData,10000);
    LoadReadMe();
});


function RefreshData(){
    //console.log("refresh display");
    //info.model.pullData();
    info.display();
    plugins.display();
    extensions.display();
}




var already_loaded = false;

function LoadReadMe(){
    settings.getVar("type",data=>{
        console.log("load readme type",data);
        var md = "/DEVICE.md";
        if(data == "hub"){
            md = "/HUB.md";;
        }
        //md = "README.md";
        $.get(md).done(json=>{
            //$("#about").html(markdown.toHTML(json));
            $("#about").html(marked(json));
            plugins.model.getData(data=>{
                console.log("load readme plugins",data);
                if(!already_loaded){
                    data.plugins.forEach(plugin=>{
                        $.get(plugin.local+"ABOUT.md").done(json=>{
                            $("#about").append(marked(json));
                        });
                    });    
                }
                already_loaded = true;
            },true);
        }).fail(e=>{
            console.log(e);
        });        
    });
}
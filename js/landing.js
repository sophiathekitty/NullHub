var info = new View(new Model("info","/api/info/","/api/info/"));
var plugins = new View(new Collection("plugins","plugin","/api/info/plugins/","/api/info/plugins/"),null,new Template("plugin","/templates/items/plugin.html"));
var extensions = new View(new Collection("extensions","extension","/api/info/extensions/","/api/info/extensions/"),null,new Template("template","/templates/items/extension.html"));
var settings = new Settings();
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
            //marked(json);
        }).fail(e=>{
            console.log(e);
        });        
    });
}
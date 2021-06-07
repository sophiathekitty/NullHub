var info = new View(new Model("info","/api/info","/api/info"));
var plugins = new View(new Collection("plugins","plugin","/api/info/plugins","/api/info/plugins"),null,new Template("plugin","/templates/items/plugin.html"));
var extensions = new View(new Collection("extensions","extension","/api/info/extensions","/api/info/extensions"),null,new Template("template","/templates/items/extension.html"));
//info.model.pull_delay = 60000;
$(document).ready(function(){
    // load the server info
    info.display();
    plugins.build();
    extensions.build();
    //window.refreshData = setInterval(RefreshData,2000);
});


function RefreshData(){
    //console.log("refresh display");
    //info.model.pullData();
    info.display();
    plugins.display();
    extensions.display();
}





function LoadReadMe(){
    $.get("/README.md").done(json=>{
    $("#about").html(markdown.toHTML(json));
    }).fail(e=>{
        console.log(e);
    });
}
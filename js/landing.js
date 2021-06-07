var info = new View(new Model("info","/api/info","/api/info"));
var plugins = new View(new Collection("plugins","plugin","/api/info/plugins","/api/info/plugins"),null,new Template("plugin","/templates/items/plugin.html"));
$(document).ready(function(){
    // load the server info
    info.display();
    plugins.build();
});

function LoadReadMe(){
    $.get("/README.md").done(json=>{
    $("#about").html(markdown.toHTML(json));
    }).fail(e=>{
        console.log(e);
    });

}
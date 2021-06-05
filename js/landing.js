$(document).ready(function(){
    // load the server info
    LoadServerInfo();
});

function LoadServerInfo(){
    var info = new View(new Model("info","/api/info","/api/info"));
    info.display();
}
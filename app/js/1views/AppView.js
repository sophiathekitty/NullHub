class AppView extends View {
    constructor(){
        super(null);
    }
    build(){
        Settings.loadVar("name",name=>{
            Settings.loadVar("type",type=>{
                Settings.loadVar("server",server=>{
                    $("head title").html(server+" ["+type+"] "+name);
                    $("body").attr("server",server);
                    $("body").attr("type",type);
                });
            });
            Settings.loadVar("room_id",room_id=>{
                $("#floors").attr("room_id",room_id);
            });
            $("header h1").html(name);
        });
    }
}
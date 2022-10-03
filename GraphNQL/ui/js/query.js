function TestPostJSON(SendInfo){
    console.log("query",SendInfo);
    $.ajax({
        type: 'post',
        url: '/GraphNQL/?DEBUG=true',
        data: JSON.stringify(SendInfo),
        dataType: 'json',
        success: function (data) {
            console.log("response",data);
        }
    });
}
$(function(){
    TestPostJSON({
        "Rooms":{
            "WeMoLights":{
                "room_id":":parent:id",
                "mac_address":"*",
                "name":"*",
                "type":"*",
                "subtype":"*",
                "state":"*",
                "target_state":"*",
                "error":"*"
            },
            "Displays":{
                "room_id":":parent:id",
                "mac_address":"*",
                "name":"*",
                "type":"*",
                "state":"*"
            },
            "TemperatureSensors": {
                "room_id":":parent:id",
                "id":"*",
                "name":"*",
                "temp":"*",
                "temp_max":"*",
                "temp_min":"*",
                "hum":"*",
                "hum_max":"*",
                "hum_min":"*",
                "error":"ok"
            }    
        },
        "Testing": {},
        "Users":{
            "id":"*",
            "username":"*",
            "level":"*"
        },
        "WeMoDeepArchives":{}
    });
});
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
            "id":"*",
            "name":"*",
            "floor":"-1",
            "WeMoLights":{
                "room_id":":parent:id",
                "mac_address":"*",
                "name":"*",
                "type":"*",
                "subtype":"*",
                "state":"*",
                "target_state":"*",
                "error":"*"
            }    
        }
    });
});
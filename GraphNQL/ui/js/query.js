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
            "butts":"*",
            "WeMoLights":{
                "mac_address":"*",
                "name":"*",
                "room_id":":parent:id",
                "state":"*"
            }
        }
    });
});
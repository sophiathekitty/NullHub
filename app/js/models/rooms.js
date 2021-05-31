console.log("rooms.js included?");
var rooms = new Collection('rooms','room','/api/rooms','api/rooms','room_id');
console.log(rooms);
console.log("hello?");
console.log(rooms.name);
rooms.getData(data=>{
    if(data && data[rooms.name]){
        console.log(data[rooms.name]);
        data[rooms.name].forEach(room => {
            console.log(room);
            Object.keys(room).forEach(key=>{
                console.log(key,room[key]);
            });
        });
    }
    if(data && data[rooms.item_name]){
        console.log(data[rooms.item_name]);
    }
});
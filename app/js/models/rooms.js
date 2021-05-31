class Rooms extends Collection {
    constructor(){
        super('rooms','room','/api/rooms','api/rooms','room_id');
    }
}
/*
rooms.getData(data=>{
    if(data && data[rooms.name]){
        //console.log(data[rooms.name]);
        data[rooms.name].forEach(room => {
            //console.log(room);
            Object.keys(room).forEach(key=>{
                //console.log(key,room[key]);
            });
        });
    }
    if(data && data[rooms.item_name]){
        //console.log(data[rooms.item_name]);
    }
});
*/
class Model {
    static storage = window.localStorage;
    static server_online = true;
    static server_errors = 0;
    constructor(name,get_url,save_url,cache_time){
        this.prefix = this.prefix;
        this.name = name;
        this.get_url = get_url;
        this.save_url = save_url;
        this.pull_delay = cache_time;
        this.debug = false;
        //this.pulled = new Date(Model.storage.getItem(this.prefix+this.name+"_date"));
        if(this.debug){
            console.log("model created");
        }
        try{
            this.pullData(data=>{
                if(this.debug){
                    console.log(data);
                }
            })    
        } catch(err) {
            console.error(err);
        }
    }
    getData(callBack){
        var date = new Date();
        if( Model.server_online &&
            (
                this.pulled == null || 
                this.pulled == undefined || 
                date.getTime() > this.pulled.getTime() + this.pull_delay
            )
        ){
            this.pullData(callBack);
        }
        if(Model.storage.getItem(this.name+"_changed") === null)
            callBack(JSON.parse(Model.storage.getItem(this.prefix+this.name)));
        else
            callBack(JSON.parse(Model.storage.getItem(this.prefix+this.name+"_changed")));
    }
    pullData(callBack){
        $.get(this.get_url).done(json=>{
            this.pulled = new Date();
            if(this.cache_time > 0){
                Model.storage.setItem(this.prefix+this.name, JSON.stringify(json));
                //Model.storage.setItem(this.prefix+this.name+"_date", this.pulled.toString());    
            }
            Model.server_errors--;
            if(Model.server_errors < 0) Model.server_errors = 0;
            callBack(json);
        }).fail(e=>{
            if(this.debug){
                console.error(e);
            }
            Model.server_errors++;
            callBack(JSON.parse(Model.storage.getItem(this.prefix+this.name)));
        });
    }
    setData(data){
        Model.storage.setItem(this.name+"_changed",data);
    }
    pushData(callBack,errorCallback,failCallback){
        var myData = JSON.parse(Model.storage.getItem(this.prefix+this.name+"_changed"));
        if(this.debug){
            console.log("module push");
            console.log(myData);
        }
        if(myData && Model.server_online){
            $.ajax({
                type: "POST",
                url: this.save_url,
                dataType: "json",
                data: myData,
                success: data=>{
                    if(this.debug){
                        console.log(this.name+": push success");
                    }
                    if(this.errors < 0) this.errors = 0;
                    if(callBack) callBack(data);
                    Model.storage.getItem(this.name+"_changed",data);
                    Model.server_errors--;
                    if(Model.server_errors < 0) Model.server_errors = 0;
                },
                error: e=>{
                    Model.server_errors++;
                    if(this.debug) {
                        console.log(this.name+": push error");
                        console.log(e);    
                    }
                    if(errorCallback) errorCallback(e);
                },
                fail: res=>{
                    Model.server_errors++;
                    if(this.debug){
                        console.log(this.name+": push fail");
                        console.log(res);
                    }
                    if(failCallback) failCallback(res);
                }
            });    
        }
    }
}

class Collection extends Model {
    constructor(collection_name,item_name,get_url,save_url,id_name = "id"){
        super(collection_name, get_url, save_url);
        this.item_name = item_name;
        this.id_name = id_name;
    }
    getItem(id,callBack){
        this.getData(data=>{
            data[this.collection_name].forEach(item => {
                if(item[this.id_name] == id) callBack(item);
            });
        })
    }
    setItem(item){
        this.getData(data=>{
            var isNew = true;
            for(i = 0; i < data[this.collection_name].length; i++){
                if(data[this.collection_name][i][this.id_name] == item[this.id_name]){
                    data[this.collection_name][i] = item;
                    data[this.collection_name][i].edited = true;
                    isNew = false;
                }
            }
            if(isNew){
                item.isNew = true;
                data[this.collection_name].push(item);
            }
            Model.storage.setItem(this.name+"_changed",data);
        })
    }
}
class Model {
    static storage = window.localStorage;
    static server_online = true;
    static server_errors = 0;
    constructor(name,get_url,save_url,cache_time){
        self.name = name;
        self.get_url = get_url;
        self.save_url = save_url;
        self.pull_delay = cache_time;
        self.debug = false;
        //self.pulled = new Date(Model.storage.getItem("model_"+self.name+"_date"));
        if(self.debug){
            console.log("model created");
        }
        self.pullData(data=>{
            if(self.debug){
                console.log(data);
            }
        })
    }
    getData(callBack){
        var date = new Date();
        if( Model.server_online &&
            (
                self.pulled == null || 
                self.pulled == undefined || 
                date.getTime() > self.pulled.getTime() + self.pull_delay
            )
        ){
            self.pullData(callback);
        }
        if(Model.storage.getItem(self.name+"_changed") === null)
            callBack(JSON.parse(Model.storage.getItem("model_"+self.name)));
        else
            callBack(JSON.parse(Model.storage.getItem("model_"+self.name+"_changed")));
    }
    pullData(callBack){
        $.get(self.get_url).done(json=>{
            self.pulled = new Date();
            if(self.cache_time > 0){
                Model.storage.setItem("model_"+self.name, JSON.stringify(json));
                //Model.storage.setItem("model_"+self.name+"_date", self.pulled.toString());    
            }
            Model.server_errors--;
            if(Model.server_errors < 0) Model.server_errors = 0;
            callBack(json);
        }).fail(e=>{
            if(self.debug){
                console.error(e);
            }
            Model.server_errors++;
            callBack(JSON.parse(Model.storage.getItem("model_"+self.name)));
        });
    }
    setData(data){
        Model.storage.setItem(self.name+"_changed",data);
    }
    pushData(callBack,errorCallback,failCallback){
        var myData = JSON.parse(Model.storage.getItem("model_"+self.name+"_changed"));
        if(self.debug){
            console.log("module push");
            console.log(myData);
        }
        if(myData && Model.server_online){
            $.ajax({
                type: "POST",
                url: self.save_url,
                dataType: "json",
                data: myData,
                success: data=>{
                    if(self.debug){
                        console.log(self.name+": push success");
                    }
                    if(self.errors < 0) self.errors = 0;
                    if(callBack) callBack(data);
                    Model.storage.getItem(self.name+"_changed",data);
                    Model.server_errors--;
                    if(Model.server_errors < 0) Model.server_errors = 0;
                },
                error: e=>{
                    Model.server_errors++;
                    if(self.debug) {
                        console.log(self.name+": push error");
                        console.log(e);    
                    }
                    if(errorCallback) errorCallback(e);
                },
                fail: res=>{
                    Model.server_errors++;
                    if(self.debug){
                        console.log(self.name+": push fail");
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
        self.item_name = item_name;
        self.id_name = id_name;
    }
    getItem(id,callBack){
        this.getData(data=>{
            data[self.collection_name].forEach(item => {
                if(item[self.id_name] == id) callBack(item);
            });
        })
    }
    setItem(item){
        this.getData(data=>{
            var isNew = true;
            for(i = 0; i < data[self.collection_name].length; i++){
                if(data[self.collection_name][i][self.id_name] == item[self.id_name]){
                    data[self.collection_name][i] = item;
                    data[self.collection_name][i].edited = true;
                    isNew = false;
                }
            }
            if(isNew){
                item.isNew = true;
                data[self.collection_name].push(item);
            }
            Model.storage.setItem(self.name+"_changed",data);
        })
    }
}
/**
 * Model 
 * base data loading class that handles loading data from the api and caching it in local storage
 * it will load data from local storage and pull updates to this data when it's out of date
 */
class Model {
    static storage = window.localStorage;
    static server_online = true;
    static server_errors = 0;
    static constructor_requests_started = 0;
    static constructor_requests_completed = 0;
    static pull_requests_started = 0;
    static pull_requests_completed = 0;
    static push_requests_started = 0;
    static push_requests_completed = 0;
    /**
     * creates a new model
     * @param {string} name the name of the model needs to match the root property of the json api response
     * @param {string} get_url url data is loaded from
     * @param {string} save_url url to push data to
     * @param {number} cache_time how long to wait before pulling the api
     * @param {string} prefix used before name for local storage key
     * @param {bool} debug set to true to turn on console output
     */
    constructor(name,get_url,save_url,cache_time = 300000,prefix = "model_", debug = false){
        if(debug) console.log("Model::Constructor",name,get_url,save_url,cache_time,prefix, debug)
        this.prefix = prefix;
        this.name = name;
        this.get_url = get_url;
        this.save_url = save_url;
        this.pull_delay = cache_time;
        this.debug = debug;
        
        this.pulled = new Date(Model.storage.getItem(this.prefix+this.name+"_pulled"));
        if(this.debug){
            console.log("Model::"+this.name+":model created",this.prefix+this.name);
        }
        if(!(Model.storage.getItem(this.prefix+this.name) === null)){
            Model.constructor_requests_started++;
            this.pullData(data=>{
                Model.constructor_requests_completed++;
                if(Model.constructor_requests_started == Model.constructor_requests_completed){
                    //console.log("in theory all the constructor pulls have finished");
                }
                if(this.debug){
                    console.log("Model::"+this.name+":constructor pull data: ",data);
                }
            });    
        }
    }
    /**
     * gets data either from local storage cache and/or pull live data from api
     * @param {function(JSON)} callBack the data is sent to this function
     * @param {bool} only_return_once set to true so it doesn't return both the cached version and a pulled version if the cached version is out of date
     * @returns {void} will return early if set to only_return_once
     */
    getData(callBack,only_return_once = true){
        var date = new Date();
        var returns = 0;
        if( Model.server_online &&
            (
                this.pulled == null || 
                this.pulled == undefined || 
                date.getTime() > this.pulled.getTime() + this.pull_delay
            )
        ){
            //console.log(this.prefix+this.name,"pull live data");
            this.pullData(callBack);
            returns++;
        }
        //console.log("get data... last pulled: ",this.pulled);
        if(returns > 0 && only_return_once) return;
        //console.log(this.prefix+this.name,"use cached data");
        if(Model.storage.getItem(this.prefix+this.name) === null) return;
        if(Model.storage.getItem(this.prefix+this.name+"_changed") === null){
            //console.log(this.prefix+this.name,"get basic item",Model.storage.getItem(this.prefix+this.name));
            callBack(JSON.parse(Model.storage.getItem(this.prefix+this.name)));
        } else {
            //console.log(this.prefix+this.name,"get changed item",Model.storage.getItem(this.prefix+this.name+"_changed"));
            callBack(JSON.parse(Model.storage.getItem(this.prefix+this.name+"_changed")));
        }
    }
    /**
     * pulls data from the api
     * @param {function(JSON)} callBack sends pulled data to this function
     */
    pullData(callBack){
        Model.pull_requests_started++;
        $.get(this.get_url).done(json=>{
            this.pulled = new Date();
            Model.storage.setItem(this.prefix+this.name, JSON.stringify(json));
            Model.storage.setItem(this.prefix+this.name+"_pulled",this.pulled);
            Model.server_errors--;
            if(Model.server_errors < 0) Model.server_errors = 0;
            if(callBack) callBack(json);
            Model.pull_requests_completed++;
            if(Model.pull_requests_started == Model.pull_requests_completed){
                //console.log("done loading for now.... (all active requests completed)");
            }
        }).fail(e=>{
            if(this.debug){
                console.error("Model::"+this.name+":error",e);
            }
            Model.pull_requests_completed++;
            Model.server_errors++;
            if(callBack) callBack(JSON.parse(Model.storage.getItem(this.prefix+this.name)));
        });
    }
    /**
     * Saves the data object to local storage in a second record with the postfix _changed
     * @param {Object} data 
     */
    setData(data,live = false){
        if(live){
            Model.storage.setItem(this.prefix+this.name,JSON.stringify(data));
        } else {
            Model.storage.setItem(this.prefix+this.name+"_changed",JSON.stringify(data));
        }
    }
    /**
     * [untested?] pushes the data in local storage with the postfix _changed for this model to the save api
     * @param {function(JSON)} callBack called if push request was successful
     * @param {function(*)} errorCallback called if there is an error
     * @param {function(*)} failCallback called if the request failed
     */
    pushData(callBack,errorCallback,failCallback){
        var myData = JSON.parse(Model.storage.getItem(this.prefix+this.name+"_changed"));
        if(this.debug){
            console.log("Model::pushData",this.prefix+this.name+"_changed",myData);
        }
        if(myData && Model.server_online){
            Model.push_requests_started++;
            //console.error("model push ajax is likely broken... because fuck anything ever working! >:C");
            $.ajax({
                type: "GET",
                url: this.save_url,
                dataType: "json",
                data: myData,
                success: data=>{
                    if(this.debug){
                        console.log("Model::"+this.name+": push success",data);
                    }
                    if(this.errors < 0) this.errors = 0;
                    if(callBack) callBack(data);
                    //Model.storage.getItem(this.name+"_changed",data); // what? getItem(key,data???) /// should maybe be clearing _changed and updating the main one with the data?
                    Model.storage.setItem(this.prefix+this.name,JSON.stringify(data)); // ok. lets actually update the local data with the correct name
                    var json = JSON.parse(Model.storage.getItem(this.prefix+this.name));
                    Model.storage.removeItem(this.prefix+this.name+"_changed"); // and clear out the local changes that have now been saved
                    Model.server_errors--;
                    Model.push_requests_completed++;
                    if(Model.server_errors < 0) Model.server_errors = 0;
                },
                error: e=>{
                    Model.push_requests_completed++;
                    Model.server_errors++;
                    if(this.debug) {
                        console.error("Model::"+this.name+": push error",e);
                    }
                    if(errorCallback) errorCallback(e);
                },
                fail: res=>{
                    Model.push_requests_completed++;
                    Model.server_errors++;
                    if(this.debug){
                        console.error("Model::"+this.name+": push fail",res);
                    }
                    if(failCallback) failCallback(res);
                }
            });    
        }
    }
}
/**
 * Collection
 * adds list functions to Model
 */
class Collection extends Model {
    /**
     * creates a collection. collections default the cache time to 5 minutes
     * @param {string} collection_name the name of the root model ie: rooms <section id="rooms"></section>
     * @param {string} item_name the name of items ie: room <div class="room"></div>
     * @param {string} get_url the api url for loading data
     * @param {string} save_url the api url for saving data
     * @param {string} id_name the name of id field in the model data. ie: "id" or "hour" or "name"
     * @param {string} prefix the prefix used for the local storage key
     * @param {bool} debug set to true to turn on console output for this collection
     */
    constructor(collection_name,item_name,get_url,save_url,id_name = "id",prefix = "collection_", debug = false){
        if(debug) console.log("Collection::Constructor",collection_name,item_name,get_url,save_url,id_name,prefix, debug);
        super(collection_name, get_url, save_url,1000*60*5,prefix,debug);
        this.item_name = item_name;
        this.id_name = id_name;
        this.push_items_started = 0;
        this.push_items_completed = 0;
    }
    /**
     * gets an item from the data list
     * @param {string|number} id the item id to be loaded
     * @param {function(JSON)} callBack where the data is sent
     */
    getItem(id,callBack){
        this.getData(data=>{
            data[this.name].forEach(item => {
                if(item[this.id_name] == id) callBack(item);
            });
        },true);
    }
    /**
     * saves or adds an item to the _changed version of the data list
     * @param {json|Object} item the json object to be added to the data list
     */
    setItem(item){
        if(this.debug) console.log("Collection::setItem:",item);
        this.getData(data=>{
            if(this.debug) console.log("Collection::setItem:data:",data);
            var isNew = true;
            for(var i = 0; i < data[this.name].length; i++){
                if(data[this.name][i][this.id_name] == item[this.id_name]){
                    data[this.name][i] = item;
                    data[this.name][i].edited = true;
                    isNew = false;
                }
            }
            if(isNew){
                item.isNew = true;
                data[this.name].push(item);
            }
            if(this.debug) console.log("Collection::setItem:changed:",data);
            this.setData(data);
            //Model.storage.setItem(this.prefix+this.name+"_changed",data);
        })
    }
    /**
     * pushes the data in local storage with the postfix _changed for this model to the save api
     * @param {function(JSON)} callBack called if push request was successful
     * @param {function(*)} errorCallback called if there is an error
     * @param {function(*)} failCallback called if the request failed
     * @param {function(*)} doneCallback called if the request failed
     */
    pushData(callBack,errorCallback,failCallback,doneCallback){
        var oldData = JSON.parse(Model.storage.getItem(this.prefix+this.name));
        var allData = JSON.parse(Model.storage.getItem(this.prefix+this.name+"_changed"));
        for(var i = 0; i < allData[this.name].length; i++){
            if(JSON.stringify(oldData[this.name][i]) != JSON.stringify(allData[this.name][i])){
                this.push_items_started++;
                this.pushItem(allData[this.name][i],json=>{
                    this.push_items_completed++;
                    var done = this.pushDone(allData);
                    callBack(json);
                    if(done) doneCallback(json);
                },error=>{
                    this.push_items_completed++;
                    var done = this.pushDone(allData);
                    errorCallback(error);
                    if(done) doneCallback(json);
                },error=>{
                    this.push_items_completed++;
                    var done = this.pushDone(allData);
                    failCallback(error);
                    if(done) doneCallback(json);
                });
            }
        }
    }
    /**
     * checks to see if all the items have been pushed to the server
     * @param {JSON} allData the changed data
     * @returns {bool} returns true if it updated the local storage and false if there's still changes being pushed
     */
    pushDone(allData){
        if(this.debug) console.log("Collection::"+this.name+":pushDone?");
        if(this.push_items_started == this.push_items_completed){
            var date = new Date();
            if(this.debug) console.log("Collection::"+this.name+":pushDone! YES");
            Model.storage.setItem(this.prefix+this.name,JSON.stringify(allData)); // ok. lets actually update the local data with the correct name
            Model.storage.removeItem(this.prefix+this.name+"_changed"); // and clear out the local changes that have now been saved
            this.pulled = new Date(date.getTime()-(this.pull_delay*0.75));
            return true;
        }
        return false;
    }
    /**
     * push a collection item to the server
     * @param {JSON} myData the item data
     * @param {function(JSON)} callBack 
     * @param {function(*)} errorCallback 
     * @param {function(*)} failCallback 
     */
    pushItem(myData,callBack,errorCallback,failCallback){
        if(this.debug){
            console.log("Collection::pushItem",this.prefix+this.name+"_changed:item",myData);
        }
        if(myData && Model.server_online){
            Model.push_requests_started++;
            //console.error("model push ajax is likely broken... because fuck anything ever working! >:C");
            $.ajax({
                type: "GET",
                url: this.save_url,
                dataType: "json",
                data: myData,
                success: data=>{
                    if(this.debug){
                        console.log("Collection::"+this.name+": push success",data);
                    }
                    if(data[this.item_name]){
                        this.setItem(data[this.item_name]);
                    }
                    if(this.errors < 0) this.errors = 0;
                    if(callBack) callBack(data);
                    //Model.storage.getItem(this.name+"_changed",data); // what? getItem(key,data???) /// should maybe be clearing _changed and updating the main one with the data?
                    //Model.storage.setItem(this.prefix+this.name,JSON.stringify(data)); // ok. lets actually update the local data with the correct name
                    //var json = JSON.parse(Model.storage.getItem(this.prefix+this.name));
                    Model.server_errors--;
                    Model.push_requests_completed++;
                    if(Model.server_errors < 0) Model.server_errors = 0;
                },
                error: e=>{
                    Model.push_requests_completed++;
                    Model.server_errors++;
                    if(this.debug) {
                        console.error("Collection::"+this.name+": push error",e);
                    }
                    if(errorCallback) errorCallback(e);
                },
                fail: res=>{
                    Model.push_requests_completed++;
                    Model.server_errors++;
                    if(this.debug){
                        console.error("Collection::"+this.name+": push fail",res);
                    }
                    if(failCallback) failCallback(res);
                }
            });    
        }
    }
}
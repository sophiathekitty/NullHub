/**
 * a special collection for handling SettingsVars
 */
class Settings extends Collection {
    static settings;
    /**
     * setup the settings collection
     * @param {string} collection_name name of the collection
     * @param {string} item_name still not sure what item name is for?
     * @param {string} get_url the url for the api
     */
    constructor(collection_name = "settings",item_name = "setting",get_url = "/api/settings/"){
        super(collection_name,item_name,get_url,get_url,"name","settings_");
        this.pull_delay = 10000;
    }
    /**
     * static function for loading a var from the server
     * @param {string} var_name the name of the var to load
     * @param {Function} callBack do something with the var you just loaded
     */
    static loadVar(var_name,callBack){
        if(Settings.settings == null) Settings.settings = new Settings();
        Settings.settings.getVar(var_name,callBack);
    }
    /**
     * local function for loading a var from the server
     * @param {string} var_name the name of the var to load
     * @param {Function} callBack do something with the var you just loaded
     */
    getVar(var_name,callBack){
        this.getItem(var_name,data=>{
            if(data) callBack(data.value);
        });
    }
    /**
     * static function for saving a var to the server
     * @param {string} var_name the name of the var to save
     * @param {string|Number} value the value of the var being saved
     * @param {Function} callBack save was successful
     * @param {Function} errorCallback there was an error
     * @param {Function} failCallback something went wrong?
     */
    static saveVar(var_name,value,callBack,errorCallback,failCallback){
        if(Settings.settings == null) Settings.settings = new Settings();
        Settings.settings.setVar(var_name,value,callBack,errorCallback,failCallback);
    }
    /**
     * local function for saving a var to the server
     * @param {string} var_name the name of the var to save
     * @param {string|Number} value the value of the var being saved
     * @param {Function} callBack save was successful
     * @param {Function} errorCallback there was an error
     * @param {Function} failCallback something went wrong?
     */
    setVar(var_name,value,callBack,errorCallback,failCallback){
        this.debug = true;
        var date = new Date();
        this.setItem({"name":var_name,"value":value,"modified":date.toDateString()});
        //this.pushData(callBack,errorCallback,failCallback);
        var myData = {"name":var_name,"value":value};
        
        Model.push_requests_started++;
        $.ajax({  
            data: myData,
            type: 'GET',
            url: this.save_url,
            success: data=>{
                if(this.debug){
                    console.log(this.name+": push success",data);
                }
                if(this.errors < 0) this.errors = 0;
                if(callBack) callBack(data);
                Model.storage.getItem(this.name+"_changed",data);
                Model.server_errors--;
                Model.push_requests_completed++;
                if(Model.server_errors < 0) Model.server_errors = 0;
            },
            error: e=>{
                Model.push_requests_completed++;
                Model.server_errors++;
                if(this.debug) {
                    console.log(this.name+": push error");
                    console.log(e);    
                }
                if(errorCallback) errorCallback(e);
            },
            fail: res=>{
                Model.push_requests_completed++;
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
/**
 * load settings with a shared
 */
class SettingsPallet extends Settings {
    /**
     * 
     * @param {string} pallet_name the name of the pallet
     * @param {string} pallet_url the get_url for the pallet api
     */
    constructor(pallet_name,pallet_url){
        super(pallet_name,"settings",pallet_url);
    }
}
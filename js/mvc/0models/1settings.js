class Settings extends Collection {
    constructor(){
        super("settings","setting","/api/settings/","/api/settings/","name","settings_");
        this.pull_delay = 10000;
    }
    getVar(var_name,callBack){
        this.getItem(var_name,data=>{
            if(data) callBack(data.value);
        });
    }
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
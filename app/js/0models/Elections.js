/**
 * a potential elections model... right now it just has a static function
 * for appointing a new main hub
 */
class ElectionsModel extends Model {
    constructor(){
        super("elections","/api/election/","/api/election/appoint/");
    }
    /**
     * static function for appointing main hub
     * @param {string} mac_address the mac address of the new main hub
     * @param {Function} callBack save was successful
     * @param {Function} errorCallback there was an error
     * @param {Function} failCallback something went wrong?
     */
    static appoint(mac_address,callBack,errorCallback,failCallback){
        Model.push_requests_started++;
        $.ajax({  
            data: {'mac_address':mac_address},
            type: 'GET',
            url: "/api/election/appoint/",
            success: data=>{
                if(this.debug){
                    console.log(this.name+": push success",data);
                }
                if(this.errors < 0) this.errors = 0;
                if(callBack) callBack(data);
                //Model.storage.getItem(this.name+"_changed",data);
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
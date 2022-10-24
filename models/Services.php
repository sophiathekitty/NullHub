<?php
class Services extends clsModel {
    private static $instance = null;
    public static $current_service = null;
    private static $running = [];
    /**
     * @return Services|clsModel
     */
    private static function GetInstance(){
        if(is_null(Services::$instance)){
            Services::$instance = new Services();
        }
        return Services::$instance;
    }

    /**
     * start running a service
     * @param string $service_name the name of the service being tracked
     * @return array save report
     */
    public static function Start($service_name){
        Services::$current_service = $service_name;
        array_push(Services::$running,$service_name);
        Debug::LogGroup($service_name,"Started at ".date("H:i:s"));
        $services = Services::GetInstance();
        $service = $services->LoadWhere(['name'=>$service_name]);
        if(is_null($service)) {
            // create new service
            return $services->Save(['name'=>$service_name,'logs'=>date("H:i:s")."::Start",'last_start'=>date("Y-m-d H:i:s")]);
        }
        $service['last_start'] = date("Y-m-d H:i:s");
        $service['logs'] = date("H:i:s")."::Start";
        return $services->Save($service,['name'=>$service_name]);
    }
    /**
     * service has finished running
     * @param string $service_name the name of the service being tracked
     * @return array save report
     */
    public static function Complete($service_name){
        Services::$current_service = null;
        array_pop(Services::$running);
        Debug::LogGroup($service_name,"Completed at ".date("H:i:s"));
        $services = Services::GetInstance();
        $service = $services->LoadWhere(['name'=>$service_name]);
        if(is_null($service)) return null;
        $service['last_done'] = date("Y-m-d H:i:s");
        $service['logs'] .= "\n".date("H:i:s")."::Done";
        return $services->Save($service,['name'=>$service_name]);
    }
    /**
     * add a message to the service logs
     * @param string $service_name the name of the service being tracked
     * @param string $message the message to add to the logs
     * @return array save report
     */
    public static function Log($service_name,$message){
        Debug::LogGroup($service_name,date("H:i:s")."::".$message);
        if(count(Services::$running) == 0) return null;
        //if(is_null(Services::$current_service)) return null;
        $services = Services::GetInstance();
        $service = $services->LoadWhere(['name'=>$service_name]);
        if(is_null($service)) return null;
        $service['logs'] .= "\n".date("H:i:s")."::".$message;
        return $services->Save($service,['name'=>$service_name]);
    }

    public $table_name = "Services";
    public $fields = [
        [
            'Field'=>"name",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"logs",
            'Type'=>"text",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"last_start",
            'Type'=>"datetime",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"last_done",
            'Type'=>"datetime",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ]
    ];
}

if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new Services();
}
?>
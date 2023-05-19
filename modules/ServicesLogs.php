<?php
/**
 * module for getting services info in nice formats for json apis
 */
class ServicesLogs{
    /**
     * get a list of the services with a link to their logs
     */
    public static function ServicesList(){
        $services = Services::AllServices();
        for($i = 0; $i < count($services); $i++){
            $services[$i]['logs_url'] = "http://".LocalIp()."/api/services/log/?name=".urldecode($services[$i]['name']);
        }
        return $services;
    }
    public static function ServiceLogs($service_name){
        $service = Services::ServiceName($service_name);
        $logs = explode("\n",$service['logs']);
        $service['logs'] = [];
        foreach($logs as $log){
            list($time,$message) = explode("::",$log,2);
            $service['logs'][] = ['time'=>$time,'message'=>$message];
        }
        return $service;
    }
}
?>
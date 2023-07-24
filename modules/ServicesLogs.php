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
        $service['status'] = "ok";
        $start_time = strtotime($service['last_start']);
        $done_time = strtotime($service['last_done']);
        if($start_time > $done_time){
            // is running? or not finishing?
            if($start_time - $done_time > MinutesToSeconds(15)) $service['status'] = "error";
            else $service['status']= "running";
        }
        foreach($logs as $log){
            list($time,$message) = explode("::",$log,2);
            $type = "log";
            if (substr($message, 0, 7) === "[error]") {
                $type = "error";
                $message = substr($message, 7);
            }
            if (substr($message, 0, 6) === "[warn]") {
                $type = "warning";
                $message = substr($message, 6);
            }
            if($message == "Start") $type = "start";
            if($message == "Done") $type = "done";
            // The regex pattern
            $pattern = '/\b(\w+(?:::\w+)+)\b/';
            preg_match_all($pattern,$message,$matches);
            $trace = "";
            if(count($matches)){
                $trace = $matches[0][0];
                $message = substr($message, strlen($trace));
            }
            $service['logs'][] = ['time'=>$time, 'type'=>$type,'message'=>$message,'trace'=>$trace];
        }
        return $service;
    }
}
?>
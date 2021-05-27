<?php
class RemoteTasks {
    public static function PullRemoteTasks(){
        if(Servers::IsHub()) return null;
        $hub = Servers::GetHub();
        if(is_null($hub)) return null;
        $url = "http://".$hub['url']."/api/tasks/";
        $info = file_get_contents($url);
        $data = json_decode($info,true);
        foreach($data['tasks'] as $task){
            //print_r($task);
            Tasks::SaveTask($task);
        }
    }
}
?>
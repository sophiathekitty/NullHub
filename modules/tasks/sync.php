<?php
/**
 * handles remote tasks
 */
class RemoteTasks {
    /**
     * pull remote tasks
     */
    public static function PullRemoteTasks(){
        if(Servers::IsMain()) return null;
        $report = [];
        $data = ServerRequests::LoadHubJSON("/api/tasks/");
        foreach($data['tasks'] as $task){
            //print_r($task);
            $report[] = Tasks::SaveTask($task);
        }
        return $report;
    }
}
?>
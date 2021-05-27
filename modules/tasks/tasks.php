<?php
class TaskManager {
    private static $tasks = null;
    public static function GetInstance(){
        if(is_null(TaskManager::$tasks)){
            TaskManager::$tasks = new Tasks();
        }
        return TaskManager::$tasks;
    }
    public static function AutomateTasks(){
        echo "Automate Task Notification<br>";
        // this will need to be extended to automate local tasks
    }
    public static function CompleteTask($task_id,$completed_by){
        $tasks = TaskManager::GetInstance();
        $tasks->UpdateTask($task_id,[
            'completed_by'=>$completed_by,
            'completed'=>date("Y-m-d H:i:s")
        ]);
        return $tasks->LoadById($task_id);
    }
    public static function TaskRoomNotification($room_id){
        $tasks = TaskManager::GetInstance();

        $tasks = $tasks->LoadAllTasksRoom($room_id);
        $notifications = [];
        $soon = time()+(60*5);
        $later = time()-(60*240);
    
    
        foreach($tasks as $task){
            if(is_null($task['due']) || strtotime($task['due']) < $soon){
                if(is_null($task['completed']) || strtotime($task['completed']) > $later){
                    $notifications[] = $task;
                }
            }
        }
        return $notifications;
    }    
}
?>
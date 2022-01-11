<?php
/**
 * handle stuff with tasks
 */
class TaskManager {
    /**
     * in theory this should be called by a service and do automated task generation and completion?
     * @todo this will need to be extended to automate local tasks?
     */
    public static function AutomateTasks(){
        echo "Automate Task Notification<br>";
        // this will need to be extended to automate local tasks
    }
    /**
     * complete a task
     * @param int $task_id the id of the task
     * @param int $completed_by user id of who completed task
     * @return array the completed task's data array
     */
    public static function CompleteTask($task_id,$completed_by){
        $task = Tasks::LoadTaskId($task_id);
        $task['completed_by'] = $completed_by;
        $task['completed'] = date("Y-m-d H:i:s");
        Tasks::SaveTask($task);
        return Tasks::LoadTaskId($task_id);
    }
    /**
     * task notifications for a room
     * @param int $room_id the id of the room
     * @return array array of task that are due for a room
     */
    public static function TaskRoomNotification($room_id){

        $tasks = Tasks::LoadAllTasksRoom($room_id);//$tasks->LoadAllTasksRoom($room_id);
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
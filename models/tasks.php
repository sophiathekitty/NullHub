<?php
/**
 * tasks model
 */
class Tasks extends clsModel {
    public $table_name = "Tasks";
    public $fields = [
        [
            'Field'=>"id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>"auto_increment"
        ],[
            'Field'=>"room_id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"assigned_to",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"completed_by",
            'Type'=>"int(11)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"name",
            'Type'=>"varchar(50)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"skipped",
            'Type'=>"tinyint(1)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"created",
            'Type'=>"datetime",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"current_timestamp()",
            'Extra'=>""
        ],[
            'Field'=>"due",
            'Type'=>"datetime",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"completed",
            'Type'=>"datetime",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ]
    ];
    private static $tasks;
    /**
     * get the instance
     * @return Tasks|clsModel
     */
    private static function GetInstance(){
        if(is_null(Tasks::$tasks)) Tasks::$tasks = new Tasks();
        return Tasks::$tasks;
    }
    /**
     * load active tasks
     * @return array array of tasks where completed is null
     */
    public static function LoadActiveTasks(){
        $tasks = Tasks::GetInstance();
        return $tasks->LoadWhere(['completed'=>null]);
    }
    /**
     * load task by id
     * @param int $task_id the id
     * @return array the task with task_id
     */
    public static function LoadTaskId($task_id){
        $tasks = Tasks::GetInstance();
        return $tasks->LoadById($task_id);
    }
    /**
     * load all the tasks for a room
     * @param int $room_id the room id
     * @return array array of task data arrays
     */
    public static function LoadAllTasksRoom($room_id){
        $tasks = Tasks::GetInstance();
        return $tasks->LoadAllWhere(['room_id'=>$room_id]);
    }
    /**
     * load today's active tasks
     * @return array array of tasks where completed is null and due is today? (looks like it's due => right now?)
     */
    public static function LoadActiveTasksToday(){
        $tasks = Tasks::GetInstance();
        return $tasks->LoadAllWhere(['completed'=>null,'due'=>date("Y-m-d H:i:s")]);
    }
    /**
     * load due active tasks
     * @return array array of tasks where completed is null and due is before now
     */
    public static function LoadDueTasks(){
        $tasks = Tasks::GetInstance();
        return $tasks->LoadWhereFieldBefore(['completed'=>null],"due",date("Y-m-d H:i:s"));
    }
    /**
     * save task
     * @param array $data the data array for a task
     */
    public static function SaveTask($data){
        $tasks = Tasks::GetInstance();
        $tasks->PruneField('due',DaysToSeconds(5));
        $data = $tasks->CleanDataSkipId($data);
        //print_r($data);
        $task = $tasks->LoadWhere(['name'=>$data['name'],'due'=>$data['due']]);
        if(is_null($task)){            
            return $tasks->Save($data);
        }
        return $tasks->Save($data,['name'=>$data['name'],'due'=>$data['due']]);
    }
}


if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new Tasks();
}

?>
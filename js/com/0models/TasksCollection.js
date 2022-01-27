class TasksCollection extends Collection {
    static debug_tasks = true;
    constructor(debug = TasksCollection.debug_tasks){
        if(debug) console.log("TasksCollection::Constructor");
        super("tasks","task","/api/tasks/","/api/tasks/save","id","collection_",debug);
    }
    activeTasks(callBack){
        this.getData(json=>{
            var tasks = [];
            json.tasks.forEach(task=>{
                if(task.completed == null){
                    tasks.push(task);
                }
            });
            callBack(tasks);
        });
    }
    completeTask(completed_by,task_id,doneCallback){
        this.getItem(task_id,task=>{
            task.completed_by = completed_by;
            task.completed = this.dateTimeString();
            if(this.debug) console.log("TasksCollection::CompleteTask",task);
            this.setItem(task);
            this.pushData(null,null,null,doneCallback);
        });
    }
    skipTask(completed_by,task_id,doneCallback){
        this.getItem(task_id,task=>{
            task.completed_by = completed_by;
            task.skipped = this.dateTimeString();
            if(this.debug) console.log("TasksCollection::SkipTask",task);
            this.setItem(task);
            this.pushData(null,null,null,doneCallback);
        });
    }
    dateTimeString(){
        var date = new Date();
        var year = date.getFullYear();
        var month = date.getMonth()+1;
        if(month < 10) month = "0"+month;
        var day = date.getDate();
        if(day < 10) day = "0"+day;
        var hours = date.getHours();
        if(hours < 10) hours = "0"+hours;
        var minutes = date.getMinutes();
        if(minutes < 10) minutes = "0"+minutes;
        var seconds = date.getSeconds();
        if(seconds < 10) seconds = "0"+seconds;
        return year+"-"+month+"-"+day+" "+hours+":"+minutes+":"+seconds;
    }
}
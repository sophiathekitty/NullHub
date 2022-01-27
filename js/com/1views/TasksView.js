class TasksView extends View {
    constructor(debug = TasksCollection.debug_tasks){
        super(new TasksCollection(),null, new Template("task_item","/templates/items/task.html"), 60000, debug);
    }
    build(){
        this.display();
    }
    display(){
        if(this.model && this.item_template){
            UserController.userId(user_id=>{
                this.item_template.getData(html=>{
                    this.model.activeTasks(tasks=>{
                        if(this.debug) console.log("TasksView::Display-tasks",tasks);
                        $("header .tasks").html("");
                        tasks.forEach(task=>{
                            if(this.debug) console.log("TasksView::Display-task",task);
                            $(html).appendTo("header .tasks").attr("task_id",task.id).attr("app",task.app).attr("task",task.task);
                            $("header .tasks [task_id="+task.id+"] [var=name]").html(task.name);
                            $("header .tasks [task_id="+task.id+"] a").attr("task_id",task.id);
                            $("header .tasks [task_id="+task.id+"] a").attr("completed_by",user_id);
                            $("header .tasks [task_id="+task.id+"] [var=due]").attr("val",task.due);
                            $("header .tasks [task_id="+task.id+"] [var=due]").html(this.DateTimeToTimeString(task.due));
                            
                        });
                    });
                });    
            });
        }
    }
}
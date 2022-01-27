class TasksController extends Controller {
    static instance = new TasksController();
    constructor(debug = TasksCollection.debug_tasks){
        if(debug) console.log("TaskController::Constructor");
        super(new TasksView(),debug);
    }
    ready(){
        this.view.build();
        this.refreshInterval();
        this.click(".tasks","a",e=>{
            e.preventDefault();
            var action = $(e.currentTarget).attr("action");
            var task_id = $(e.currentTarget).attr("task_id");
            var completed_by = $(e.currentTarget).attr("completed_by");
            if(this.debug) console.log("TaskController::Ready::Click",action,task_id,completed_by);
            if(action == "complete") this.view.model.completeTask(completed_by,task_id,json=>{
                if(this.debug) console.log("TaskController::Ready::Click:Complete:Done",json);
                this.view.model.pullData(fresh=>{
                    if(this.debug) console.log("TaskController::Ready::Click:Complete:Done:Refresh",fresh);
                    this.view.display();
                });
            });
            if(action == "skip") this.view.model.skipTask(completed_by,task_id,json=>{
                if(this.debug) console.log("TaskController::Ready::Click:Skip:Done",json);
                this.view.model.pullData(fresh=>{
                    if(this.debug) console.log("TaskController::Ready::Click:Skip:Done:Refresh",fresh);
                    this.view.display();
                });
            });
        });
    }
    refresh(){
        if(this.debug) console.log("TaskController::Refresh");
        this.view.display();
    }
}
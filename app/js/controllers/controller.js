class Controller {
    constructor(view){
        this.view = view;
    }
    ready(){
        throw "Override ready function to setup listeners";
    }
    click(selector, callBack){
        this.listenForEvent("click",selector,callBack);
    }
    click(selector, child_selector, callBack){
        this.listenForEvent("click",selector,child_selector,callBack);
    }
    change(selector, callBack){
        this.listenForEvent("change",selector,callBack);
    }
    change(selector, child_selector, callBack){
        this.listenForEvent("change",selector,child_selector,callBack);
    }
    listenForEvent(event, selector, callBack){
        $(selector).on(event,child_selector,callBack);
    }
    listenForEvent(event, selector, child_selector, callBack){
        $(selector).on(event,child_selector,callBack);
    }
}
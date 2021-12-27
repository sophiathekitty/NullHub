/**
 * class for handling interaction events
 */
class Controller {
    /**
     * create a new controller
     * @param {View} view the view being controlled by this controller
     */
    constructor(view,debug = false){
        if(debug){
            console.log("Controller::Constructor");
        }
        this.view = view;
        this.view.controller = this;
        this.debug = debug;
        //console.log("Controller::Constructor",this.view.controller);
        //$(document).ready(function(){ this.ready(); });
        this.listenForEvent("ready",document,()=>{
            this.ready();
        })
    }
    /**
     * Override ready() function to setup listeners for controller
     */
    ready(){
        if(this.debug){
            throw "Override ready function to setup listeners";
        }
    }
    /**
     * adds an OnClick listener to elements
     * @param {string} selector the elements that will be clicked on
     * @param {Function} callBack what to do when clicked on
     */
    click(selector, callBack){
        this.listenForEvent("click",selector,callBack);
    }
    /**
     * adds an OnClick listener to a child element
     * @param {string} selector the main selector
     * @param {string} child_selector the child element selector
     * @param {Function} callBack  what to do when clicked
     */
    click(selector, child_selector, callBack){
        this.listenForEvent("click",selector,child_selector,callBack);
    }
    /**
     * adds an OnChanged listener to an element. for when form elements have changed
     * @param {string} selector the element to listen to
     * @param {Function} callBack what to do when changed
     */
    change(selector, callBack){
        this.listenForEvent("change",selector,callBack);
    }
    /**
     * adds an OnChanged listener to a child element
     * @param {string} selector parent element
     * @param {string} child_selector child element
     * @param {Function} callBack what to do when changed
     */
    change(selector, child_selector, callBack){
        this.listenForEvent("change",selector,child_selector,callBack);
    }
    /**
     * adds an OnFocusOOut listener to an element. for when form elements have changed
     * @param {string} selector the element to listen to
     * @param {Function} callBack what to do when changed
     */
    focusin(selector, callBack){
        this.listenForEvent("focusin",selector,callBack);
    }
    /**
     * adds an OnFocusOut listener to a child element
     * @param {string} selector parent element
     * @param {string} child_selector child element
     * @param {Function} callBack what to do when changed
     */
    focusin(selector, child_selector, callBack){
        this.listenForEvent("focusin",selector,child_selector,callBack);
    }
    /**
     * adds an OnFocusOOut listener to an element. for when form elements have changed
     * @param {string} selector the element to listen to
     * @param {Function} callBack what to do when changed
     */
    focusout(selector, callBack){
        this.listenForEvent("focusout",selector,callBack);
    }
    /**
     * adds an OnFocusOut listener to a child element
     * @param {string} selector parent element
     * @param {string} child_selector child element
     * @param {Function} callBack what to do when changed
     */
    focusout(selector, child_selector, callBack){
        this.listenForEvent("focusout",selector,child_selector,callBack);
    }

    /**
     * adds an OnScroll listener to a child element
     * @param {string} selector parent element
     * @param {string} child_selector child element
     * @param {Function} callBack what to do when changed
     */
    scroll(selector, child_selector, callBack){
        this.listenForEvent("scroll",selector,child_selector,callBack);
    }

    /**
     * this adds an event listener to an element $(selector).on(event,null,callBack);
     * @param {string} event the name of the event
     * @param {string} selector the element selector
     * @param {Function} callBack what to do when event happens
     */
    listenForEvent(event, selector, callBack){
        $(selector).on(event,null,callBack);
    }
    /**
     * this adds an event to a child element $(selector).on(event,child_selector,callBack);
     * i think this might allow for a parent element with dynamic child elements?
     * @param {string} event the name of the event
     * @param {string} selector the parent element
     * @param {string} child_selector the child element
     * @param {Function} callBack what to do when event happens
     */
    listenForEvent(event, selector, child_selector, callBack){
        $(selector).on(event,child_selector,callBack);
    }
}
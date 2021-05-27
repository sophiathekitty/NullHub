class View {
    constructor(model,template){
        self.model = model;
        self.template = template;
    }
    display(){
        throw "You need to extend display function to display view"
    }
    build(){
        throw "You need to extend display function to display view"
    }
}

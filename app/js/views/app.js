class AppView extends View{
    constructor(){
        super(new TemplateSections())
        this.sections = Array();
    }
    build(){
        this.model.getData(data=>{
            if(data != null){
                // build sections
                data[this.model.name].forEach(section=>{
                    if('name' in section){
                        console.log(section['name']);
                    }
                    if('models' in section){
                        console.log(section['models']);
                    }
                });
            }
        });

    }
}
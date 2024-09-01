/**
 * AppView
 */
class AppView extends View {
    constructor(){
        super(null,new Template("watch","/widgets/clock.php"));
    }
    /**
     * build the app view
     */
    build(){
        this.template.getData(html=>{
            $("body").html(html);
        });
    }
}
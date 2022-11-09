/**
 * main menu view handler
 */
class MenuView extends View {
    constructor(){
        super(null,new Template("main_menu","/widgets/sections/menu.php"));
    }
    /**
     * build the menu view
     */
    build(){
        if(this.template){
            this.template.getData(html=>{
                $(html).appendTo("body");
                //$("dialog#main_menu").show();
                var dialog = document.getElementById('main_menu');
                dialog.showModal();
                dialog.blur();
            });
        }
    }
    /**
     * show the main menu
     */
    show(){
        //$("#main_menu").show();
        $("dialog#main_menu").remove();
        this.build();
    }
    /**
     * close the main menu
     */
    hide(){
        $("dialog#main_menu").remove();
    }
    /**
     * show the edit field for var_name
     * @param {string} var_name 
     */
    showEditField(var_name){
        $(".value[var="+var_name+"]").hide();
        $(".edit[var="+var_name+"]").css("display","inline-block");
        $(".edit[var="+var_name+"]").focus();
    }
    /**
     * hide the edit field for var_name
     * @param {string} var_name 
     */
    hideEditField(var_name){
        $(".value[var="+var_name+"]").html($(".edit[var="+var_name+"]").val());
        $(".edit[var="+var_name+"]").hide();
        $(".value[var="+var_name+"]").show();
    }
    /**
     * show the edit field for var_name
     * @param {string} var_name 
     */
    showSelect(var_name){
        $(".value[var="+var_name+"]").hide();
        $(".edit[var="+var_name+"]").css("display","inline-block");
        $(".edit[var="+var_name+"]").focus();
    }
    /**
     * hide the edit field for var_name
     * @param {string} var_name 
     */
    hideSelect(var_name){
        $(".value[var="+var_name+"]").html($(".edit[var="+var_name+"] option:selected").text());
        $(".edit[var="+var_name+"]").hide();
        $(".value[var="+var_name+"]").show();
    }
}
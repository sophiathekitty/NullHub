class HourlyView extends View {
    /**
     * 
     * @param {HourlyChart} hourlyChart the HourlyChart model for loading data
     * @param {Template} template the main html template for holding the chart.
     * @param {Template} item_template the html template for individual hours
     * @param {number} refresh_rate how frequently to refresh the view
     */
    constructor(hourlyChart,template = null, item_template = null, refresh_rate = 60000){
        super(hourlyChart,template,item_template,refresh_rate);
    }
    /**
     * build the hourly chart
     */
    build(){
        if(this.template){
            // base template to add somewhere? might need to be done by a class that extends HourlyView
            this.template.getData(html=>{
                $(html).appendTo("#"+this.model.chart_name);
            });
        } else {
            if(this.item_template) {
                console.log("HourlyView::build::item_template--"+this.model.chart_name);
                // i think we actually have the info in the model to do the view builds?
                this.item_template.getData(html=>{
                    this.model.getData(json=>{
                        console.log(this.model.name);
                        json[this.model.item_name].forEach(hour=>{
                            var h = Number(hour.hour);
                            var am = "am";
                            if(h > 12){
                                h -= 12;
                                am = "pm";
                            }
                            if(h==0) h = 12;
                            $(html).appendTo("#"+this.model.chart_name).attr("hour",hour.hour).attr("hour_txt",h+am);
                        });
                    });
                });
            }
        }
    }
}
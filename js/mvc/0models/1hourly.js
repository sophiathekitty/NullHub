/**
 * use for hourly charts
 */
class HourlyChart extends Collection { 
    /**
     * 
     * @param {string} collection_name the name of the root model ie: rooms
     * @param {string} item_name the name of items ie: room
     * @param {string} chart_name 
     * @param {string} api 
     */
    constructor(collection_name,item_name,chart_name,api,debug = false){
        super(collection_name,item_name,api,api,"hour","chart_",debug)
        this.chart_name = chart_name;
        this.prefix = "hourly_chart_";
    }
}

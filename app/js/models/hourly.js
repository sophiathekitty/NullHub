class HourlyChart extends Collection {
    constructor(collection_name,chart_name,api){
        super(collection_name,chart_name,api,api,"hour")
        this.chart_name = chart_name;
    }
}
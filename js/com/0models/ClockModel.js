/**
 * load in the sunrise and sunset and moon data
 */
class ClockModel extends Model {
    constructor(){
        super("clock","/api/clock","/api/clock",60*15*1000);
    }
}
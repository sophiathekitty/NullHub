<?php 
class Faker {
    /**
     * make sure that this only runs on the test db and not the live one
     */
    private static function TestMode(){
        if(!defined("TEST_MODE")){
            JsonDie("can't fake data outside of test mode");
        }
    }
    /**
     * fake a single record in the table
     * @param string $table_name the name of the table to add data to
     * @param array $data the data array ['field'=>'value']
     */
    public static function FakeData($table_name,$data){
        Faker::TestMode();
        clsDB::$db_g->safe_insert($table_name,$data);
        //Debug::Log("Faker::FakeData",$table_name,$data);
    }
    /**
     * fake a series of data logs
     * @param string $table_name the name of the table to add data to
     * @param string $time_field the name of the the field that the table uses for the datetime of log
     * @param int $step the number of seconds to change each step
     * @param string $start the first datetime to use
     * @param string $stop the last datetime to use
     * @param array $data the data array ['field'=>'value']
     */
    private static $log_index = 0;
    public static function FakeLogData($table_name,$time_field,$step,$start,$stop,$data,$make_guid = false){
        Faker::TestMode();
        $time = strtotime($start);
        $stop_time = strtotime($stop);
        // make sure that we're going forward in time when we do the loop
        if($time > $stop_time) {
            //Debug::Log("Faker::FakeLogData",$table_name,"start > stop",$start,$stop);
            $start_time = $stop_time;
            $stop_time = $time;
            $time = $start_time;
        }
        if($step < 0) $step *= -1;
        // loop through the times
        $i = 1;
        while($time < $stop_time){
            $data[$time_field] = date("Y-m-d H:i:s",$time);
            if($make_guid) $data['guid'] = md5($data[$time_field].$start.$stop.Faker::$log_index++);
            clsDB::$db_g->safe_insert($table_name,$data);
            //Debug::Log("Faker::FakeLogData",$i++,$table_name,$data,"Err:".clsDB::$db_g->get_err());
            $time += $step;
        }
    }
}
?>
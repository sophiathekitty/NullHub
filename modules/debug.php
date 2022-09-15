<?php
class Debug {
    public static $debug = [];
    public static $trace = [];
    /**
     * log an item
     * @param mixed $item the object or message you want to log
     */
    public static function Trace($function){
        if(!(defined("DEBUG") || defined("TEST_MODE"))) return;
        $trace[] = $function;
    }
        /**
     * log an item
     * @param mixed $item the object or message you want to log
     */
    public static function Log(){
        if(!(defined("DEBUG") || defined("TEST_MODE"))) return;
        $args = func_get_args();
        $group = 'log';
        if(defined("TEST_MODE")){
            global $test_name;
            if(isset($test_name)) $group = $test_name;
            else $group = constant("TEST_MODE");
        }
        Debug::LogGroup($group,$args);
    }
    /**
     * log an item in a group other than 'log'
     * @param string $group the group the log will go under
     */
    public static function LogGroup($group,$item){
        if(!(defined("DEBUG") || defined("TEST_MODE"))) return;
        if(func_num_args() > 2){
            $args = func_get_args();
            $item = array_slice($args,1);
        }
        if(!isset(Debug::$debug[$group])) Debug::$debug[$group] = [];
        Debug::$debug[$group][] = $item;
    }
    /**
     * log a message or object and then output the json of the debug log and die
     * @param mixed $message the final object or message you want to log
     */
    public static function Die($message){
        if(!defined("DEBUG")) define("DEBUG",true);
        Debug::Log(['die'=>$message]);
        OutputJson(Debug::$debug);
        die();
    }
}
?>
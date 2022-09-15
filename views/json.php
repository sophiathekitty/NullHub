<?php
/**
 * generates json output from a data array
 * @param array $data the data array to display as json
 */
function OutputJson($data){
    if(defined("SETUP_MODE")) $data['setup'] = constant("SETUP_MODE");
    if(defined("DEBUG")) {
        $data['debug'] = Debug::$debug;
        $data['trace'] = Debug::$trace;
    }
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT);
    if(json_last_error())
        echo json_last_error_msg();
    if(!is_null(clsDB::$db_g)) clsDB::$db_g->CloseDB();
}
/**
 * die but with json output instead of just text
 * @param string $message the last words
 */
function JsonDie($message){
    OutputJson(["die"=>$message]);
    die();
}
?>
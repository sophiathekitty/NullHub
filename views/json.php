<?php
/**
 * generates json output from a data array
 * @param array $data the data array to display as json
 */
function OutputJson($data){
    if(defined("SETUP_MODE")) $data['setup'] = constant("SETUP_MODE");
    if(defined("DEBUG")) {
        if(constant("DEBUG") == "verbose" && isset($_GET) && count($_GET) > 0) $data['get'] = $_GET;
        if(constant("DEBUG") == "verbose" && isset($_POST) && count($_POST) > 0) $data['post'] = $_POST;
        if(isset(Debug::$debug) && count(Debug::$debug) > 0) $data['debug'] = Debug::$debug;
        if(isset(Debug::$trace) && count(Debug::$trace) > 0) $data['trace'] = Debug::$trace;
        if(constant("DEBUG") == "verbose") $data["run time"] = (microtime(true)-constant("START_TIME"));
        if(constant("DEBUG") == "verbose") $data['server'] = $_SERVER;
        if(constant("DEBUG") == "verbose" && isset($_ENV) && count($_ENV) > 0) $data['env'] = $_ENV;
        if(constant("DEBUG") == "verbose" && isset($_FILES) && count($_FILES) > 0) $data['files'] = $_FILES;
        if(constant("DEBUG") == "verbose" && isset($_REQUEST) && count($_REQUEST) > 0) $data['request'] = $_REQUEST;
        if(constant("DEBUG") == "verbose" && isset($_COOKIE) && count($_COOKIE) > 0) $data['cookie'] = $_COOKIE;
        if(constant("DEBUG") == "verbose" && isset($_SESSION) && count($_SESSION) > 0) $data['session'] = $_SESSION;
        if(constant("DEBUG") == "verbose") $data['user'] = UserSession::CleanSessionData();
    }
    if(count($_POST) > 0) $data['post'] = $_POST;
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT);
    if(json_last_error()) JsonDie(json_last_error_msg());
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
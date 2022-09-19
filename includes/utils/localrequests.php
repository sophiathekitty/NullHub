<?php

/**
 * loads nice json data array from host ip and api path
 * @param string $host the ip address
 * @param string $api the api path "/api/info/"
 * @return array associated array of json data
 */
function LoadHostJSON($host,$api){
    $url = "http://".$host.$api;
    if(defined("TEST_MODE") && $host == 'localhost'){
        if(strpos($url,"?") > -1) $url .= "&TEST_MODE=".constant("TEST_MODE");
        else $url .= "?TEST_MODE=".constant("TEST_MODE");
    } 
    if(defined("DEBUG") && $host == 'localhost'){
        if(strpos($url,"?") > -1) $url .= "&DEBUG=".constant("DEBUG");
        else $url .= "?DEBUG=".constant("DEBUG");
    } 
    $content=@file_get_contents($url);
    $json = json_decode($content,true);
    if(is_null($json)) return ['content'=>$content];
    return $json;
}
/**
 * loads nice json data array from localhost and api path
 * @param string $api the api path "/api/info/"
 * @return array associated array of json data
 */
function LoadLocalhostJSON($api){
    return LoadHostJSON('localhost',$api);
}

?>
<?php
/**
 * check google nest wifi router status
 * @return array the json array for the device status
 */
function GoogleWiFiStatus(){
    $url = "http://192.168.86.1/api/v1/status";
    $info = file_get_contents($url);
    $data = json_decode($info,true);
    return $data;
}
/**
 * check google nest wifi point status
 * @param string $ip the ip address of the google nest wifi point
 * @return array the json array for the device status
 */
function GoogleIPStatus($ip){
    $url = "http://$ip/api/v1/status";
    $info = file_get_contents($url);
    $data = json_decode($info,true);
    return $data;
}
?>
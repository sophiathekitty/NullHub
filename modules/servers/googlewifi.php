<?php
function GoogleWiFiStatus(){
    $url = "http://192.168.86.1/api/v1/status";
    $info = file_get_contents($url);
    $data = json_decode($info,true);
    return $data;
}
function GoogleIPStatus($ip){
    $url = "http://$ip/api/v1/status";
    $info = file_get_contents($url);
    $data = json_decode($info,true);
    return $data;
}
?>
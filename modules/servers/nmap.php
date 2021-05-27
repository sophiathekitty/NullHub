<?php
function nMapHosts($ip_root = "10.0.0."){
    global $my_ip;
    $raw_output = shell_exec("nmap -sP $ip_root*");
    $lines = explode("\n",$raw_output);
    $hosts = array();
    $my_ip = "";
    foreach($lines as $line){
        if(strpos($line,$ip_root) !== false){
            $pos = strpos($line,$ip_root);
            $host = substr($line,$pos);
            if(strpos($host,")") !== false){
                $host = substr($host,0,strpos($host,")"));
                if($host != $ip_root."1")
                    $my_ip = $host;
            }
            if($host != $ip_root."1")
                array_push($hosts,$host);
        }
    }
    //print_r($hosts);
    return $hosts;
}
?>
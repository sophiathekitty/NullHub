<?php
function LocalMacAddress(){
    $ifconfig = shell_exec("ifconfig");
    if(strpos($ifconfig,"inet6") > 0){
	
        $mac_address = substr($ifconfig,strpos($ifconfig,"inet6")+6,strpos($ifconfig,"prefixlen") -( strpos($ifconfig,"inet6")+6) - 2);
    }
    if($mac_address == "::1"){
        $ifconfig_split = explode($_SERVER['HTTP_HOST'],$ifconfig);
        if(count($ifconfig_split) > 1){
            $mac_address = substr($ifconfig_split[1],strpos($ifconfig_split[1],"inet6")+6,strpos($ifconfig_split[1],"prefixlen") -( strpos($ifconfig_split[1],"inet6")+6) - 2);
    
        }
    }
    return $mac_address;
}
function LocalIp(){
    $ip = eth0Ip();
    if($ip) return $ip;
    $ip = wlan0Ip();
    if($ip) return $ip;
    return enp4s0Ip();
}
function LocalMac(){
    $mac = eth0Mac();
    if($mac) return $mac;
    $mac = wlan0Mac();
    if($mac) return $mac;
    $mac = enp4s0Mac();
    if($mac) return $mac;
    return LocalMacAddress();
}
function wlan0Ip(){
    $ifconfig = shell_exec("ifconfig wlan0");
    return substr($ifconfig,strpos($ifconfig,"inet ")+5,strpos($ifconfig,"netmask") - ( strpos($ifconfig,"inet ")+5) - 2);
}
function wlan0Mac(){
    $ifconfig = shell_exec("ifconfig wlan0");
    return substr($ifconfig,strpos($ifconfig,"inet6")+6,strpos($ifconfig,"prefixlen") -( strpos($ifconfig,"inet6")+6) - 2);
}
function enp4s0Ip(){
    $ifconfig = shell_exec("ifconfig enp4s0");
    return substr($ifconfig,strpos($ifconfig,"inet ")+5,strpos($ifconfig,"netmask") - ( strpos($ifconfig,"inet ")+5) - 2);
}
function enp4s0Mac(){
    $ifconfig = shell_exec("ifconfig enp4s0");
    return substr($ifconfig,strpos($ifconfig,"inet6")+6,strpos($ifconfig,"prefixlen") -( strpos($ifconfig,"inet6")+6) - 2);
}
function eth0Ip(){
    $ifconfig = shell_exec("ifconfig eth0");
    return substr($ifconfig,strpos($ifconfig,"inet ")+5,strpos($ifconfig,"netmask") - ( strpos($ifconfig,"inet ")+5) - 2);
}
function eth0Mac(){
    $ifconfig = shell_exec("ifconfig eth0");
    return substr($ifconfig,strpos($ifconfig,"inet6")+6,strpos($ifconfig,"prefixlen") -( strpos($ifconfig,"inet6")+6) - 2);
}

?>
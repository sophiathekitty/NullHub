<?php
/**
 * gets the system hostname
 * @return string the hostname
 */
function Hostname(){
    $hostname = shell_exec("hostname");
    $hostname = str_replace("\n","",$hostname);
    return $hostname;
}
/**
 * use ```LocalMac()``` for best results
 * @return string local mac address
 */
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
/**
 * try to find the local ip address
 * @return string the ip address
 */
function LocalIp(){
    $ip = wlp3s0Ip();
    if(IsValidIP($ip)) return $ip;
    $ip = eth0Ip();
    if(IsValidIP($ip)) return $ip;
    $ip = wlan0Ip();
    if(IsValidIP($ip)) return $ip;
    return enp4s0Ip();
}
/**
 * check that an ip address is valid
 * @param string $ip the ip address to check
 * @return bool return true if the ip address starts with 192 or 10.
 */
function IsValidIP($ip){
    if($ip){
        $start = substr($ip,0,3);
        if($start == "192") return true;
        if($start == "10.") return true;    
    }
    return false;
}
/**
 * finds and caches the local mac address. should always return the same mac address for the device this is running on
 * @return string local mac address
 */
function LocalMac(){
    if(defined("SETUP_MODE")) return LocalMacCache();
    return Settings::LoadSettingsVar("mac_address",LocalMacCache());
}
/**
 * use ```LocalMac()``` for best results
 * @return string local mac address
 */
function LocalMacCache(){
    $mac = wlp3s0Mac();
    if($mac) return $mac;
    $mac = eth0Mac();
    if($mac) return $mac;
    $mac = wlan0Mac();
    if($mac) return $mac;
    $mac = enp4s0Mac();
    if($mac) return $mac;
    return LocalMacAddress();
}
/**
 * use ```LocalIP()``` for best results
 * @return string ip address or junk
 */
function wlan0Ip(){
    $ifconfig = shell_exec("ifconfig wlan0");
    return substr($ifconfig,strpos($ifconfig,"inet ")+5,strpos($ifconfig,"netmask") - ( strpos($ifconfig,"inet ")+5) - 2);
}
/**
 * use ```LocalMac()``` for best results
 * @return string local mac address
 */
function wlan0Mac(){
    $ifconfig = shell_exec("ifconfig wlan0");
    if(strpos($ifconfig,"ether") > -1){
        return substr($ifconfig,strpos($ifconfig,"ether")+6,strpos($ifconfig,"txqueuelen") -( strpos($ifconfig,"ether")+6) - 2);
    }
    return substr($ifconfig,strpos($ifconfig,"inet6")+6,strpos($ifconfig,"prefixlen") -( strpos($ifconfig,"inet6")+6) - 2);
}
/**
 * use ```LocalIP()``` for best results
 * @return string ip address or junk
 */
function enp4s0Ip(){
    $ifconfig = shell_exec("ifconfig enp4s0");
    return substr($ifconfig,strpos($ifconfig,"inet ")+5,strpos($ifconfig,"netmask") - ( strpos($ifconfig,"inet ")+5) - 2);
}
/**
 * use ```LocalMac()``` for best results
 * @return string local mac address
 */
function enp4s0Mac(){
    $ifconfig = shell_exec("ifconfig enp4s0");
    if(strpos($ifconfig,"ether") > -1){
        return substr($ifconfig,strpos($ifconfig,"ether")+6,strpos($ifconfig,"txqueuelen") -( strpos($ifconfig,"ether")+6) - 2);
    }
    return substr($ifconfig,strpos($ifconfig,"inet6")+6,strpos($ifconfig,"prefixlen") -( strpos($ifconfig,"inet6")+6) - 2);
}
/**
 * use ```LocalIP()``` for best results
 * @return string ip address or junk
 */
function wlp3s0Ip(){
    $ifconfig = shell_exec("ifconfig wlp3s0");
    return substr($ifconfig,strpos($ifconfig,"inet ")+5,strpos($ifconfig,"netmask") - ( strpos($ifconfig,"inet ")+5) - 2);
}
/**
 * use ```LocalMac()``` for best results
 * @return string local mac address
 */
function wlp3s0Mac(){
    $ifconfig = shell_exec("ifconfig wlp3s0");
    if(strpos($ifconfig,"ether") > -1){
        return substr($ifconfig,strpos($ifconfig,"ether")+6,strpos($ifconfig,"txqueuelen") -( strpos($ifconfig,"ether")+6) - 2);
    }
    return substr($ifconfig,strpos($ifconfig,"inet6")+6,strpos($ifconfig,"prefixlen") -( strpos($ifconfig,"inet6")+6) - 2);
}
/**
 * use ```LocalIP()``` for best results
 * @return string ip address or junk
 */
function eth0Ip(){
    $ifconfig = shell_exec("ifconfig eth0");
    return substr($ifconfig,strpos($ifconfig,"inet ")+5,strpos($ifconfig,"netmask") - ( strpos($ifconfig,"inet ")+5) - 2);
}
/**
 * use ```LocalMac()``` for best results
 * @return string local mac address
 */
function eth0Mac(){
    $ifconfig = shell_exec("ifconfig eth0");
    if(strpos($ifconfig,"ether") > -1){
        return substr($ifconfig,strpos($ifconfig,"ether")+6,strpos($ifconfig,"txqueuelen") -( strpos($ifconfig,"ether")+6) - 2);
    }
    return substr($ifconfig,strpos($ifconfig,"inet6")+6,strpos($ifconfig,"prefixlen") -( strpos($ifconfig,"inet6")+6) - 2);
}
/**
 * attempt to find the github url from the site manifest or just link to NullHub...
 */
function gitHubUrl(){
    global $root_path;
    if(is_file($root_path."site.webmanifest")){
        $info = file_get_contents($root_path."site.webmanifest");
        $data = json_decode($info,true);
        return $data['git'];
    }
    return "https://github.com/sophiathekitty/NullHub";
}

?>
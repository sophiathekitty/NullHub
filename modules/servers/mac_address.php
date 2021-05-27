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
?>
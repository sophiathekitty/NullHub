<?php
/**
 * try to get the info for a remote server. see if it's a null server
 * @param string $url the ip address of the remote server to check
 * @return bool|null returns true if it finds remote server info. returns null if no server found.
 */
function GetRemoteServerInfo($url){
    
    $info = file_get_contents("http://".$url."/api/info/");
    $data = json_decode($info,true);
    //echo $data->info->url;
    Debug::Log("GetRemoteServerInfo",$data);
    if(is_null($data)){
        $server = Servers::ServerIP($url);
        if(is_null($server)) return null;
        Servers::SaveServer(['mac_address'=>$server['mac_address'],'online'=>0]);
        return true;
    } 
    Servers::SaveServer($data['info']);
    //if(LocalMacAddress() != $data->info->mac_address) TellOtherDeviceAboutMe($url);
    return true;
}
/**
 * tell the device at this ip address about this server
 * @param string $url the ip address of the remote server
 */
function TellOtherDeviceAboutMe($url){
    $server = Servers::ServerMacAddress(LocalMacAddress());
    $path = "http://".$url."/api/info/servers?name=".rawurlencode(Settings::LoadSettingsVar('name'))."&type=".rawurlencode(LoadSettingVar('type'))."&mac_address=".rawurlencode(LocalMacAddress())."&url=".$server['url']."&main=".Settings::LoadSettingsVar('main')."&server=".Settings::LoadSettingsVar('server');
    //echo $path."\n";
    $info = file_get_contents($path);
    $data = json_decode($info);
    Debug::Log("TellOtherDeviceAboutMe",$path,$data);
}
/**
 * if the main hub is currently offline try to get it's info to see if it's back online yet
 */
function CheckOfflineHub(){
    $main = Servers::GetMain();
    if($main && (int)$main['online'] == 0 && $main['mac_address'] != LocalMac()){
        Settings::SaveSettingsVar("Service::CheckOfflineHub",date("H:i:s"));
        // i'm not the main so i wanna make sure it's online
        GetRemoteServerInfo($main['url']);
    }
}
?>
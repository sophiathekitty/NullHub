<?php
function GetRemoteServerInfo($url){
    
    $info = file_get_contents("http://".$url."/api/info/");
    $data = json_decode($info,true);
    //echo $data->info->url;
    print_r($data);
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
function TellOtherDeviceAboutMe($url){
    $server = Servers::ServerMacAddress(LocalMacAddress());
    $path = "http://".$url."/api/info/servers?name=".rawurlencode(Settings::LoadSettingsVar('name'))."&type=".rawurlencode(LoadSettingVar('type'))."&mac_address=".rawurlencode(LocalMacAddress())."&url=".$server['url']."&main=".Settings::LoadSettingsVar('main')."&server=".Settings::LoadSettingsVar('server');
    echo $path."\n";
    $info = file_get_contents($path);
    $data = json_decode($info);
    print_r($data);
}
function CheckOfflineHub(){
    $main = Servers::GetMain();
    if($main && (int)$main['online'] == 0 && $main['mac_address'] != LocalMac()){
        Settings::SaveSettingsVar("Service::CheckOfflineHub",date("H:i:s"));
        // i'm not the main so i wanna make sure it's online
        GetRemoteServerInfo($main['url']);
    }
}
?>
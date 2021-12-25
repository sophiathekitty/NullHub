<?php
class UserSync {
    public static function pull(){
        $hub = Servers::GetMain();
        $url = "http://".$hub['url']."/api/user/list";
        $info = file_get_contents($url);
        $data = json_decode($info,true);
        
        foreach($data['users'] as $user){
            Users::SaveUser($user);
        }
        
        return $data;
    }
}
?>
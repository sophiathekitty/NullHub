<?php
/**
 * sync users from hub
 */
class UserSync {
    /**
     * pull users from hub
     */
    public static function pull(){
        /*$hub = Servers::GetMain();
        $url = "http://".$hub['url']."/api/user/list";
        $info = file_get_contents($url);
        $data = json_decode($info,true);*/
        $data = ServerRequests::LoadMainJSON("/api/user/list");
        Debug::Log("UserSync::pull",$data);
        foreach($data['users'] as $user){
            Users::SaveUser($user);
        }
        return $data;
    }
}
?>
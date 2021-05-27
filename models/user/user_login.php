<?php
class UserLogins extends clsModel {
    public $table_name = "UserLogins";
    public $fields = [
        [
            'Field'=>"id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>"auto_increment"
        ],[
            'Field'=>"user_id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"ip",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"token",
            'Type'=>"varchar(300)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"created",
            'Type'=>"datetime",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"current_timestamp()",
            'Extra'=>""
        ],[
            'Field'=>"modified",
            'Type'=>"datetime",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"current_timestamp()",
            'Extra'=>"on update current_timestamp()"
        ]
    ];

    public function Ping($login_id){
        return $this->Save([
            'modified'=>date("Y-m-d H:i:s")
        ],[
            'id'=>$login_id
        ]);
    }
    public function LoadByIp($ip){
        return $this->LoadWhere(['ip'=>$ip]);
    }
    public function LoadByToken($token){
        return $this->LoadWhere(['token'=>$token]);
    }
    public function CreateAnonLoginSession($ip){
        $this->Save([
            "user_id" => 0,
            "ip" => $ip,
            "token" => ""
        ]);
        return $this->LoadByIp($ip);
    }
    public function LoginAnonUser($user_id,$login_id,$token){
        $this->Save([
            'user_id'=>$user_id,
            "token"=>$token
        ],[
            'id'=>$login_id
        ]);
        return $this->LoadById($login_id);
    }
    public function LogoutUserSession($id){
        $this->Save([
            "token"=>""
        ],[
            'id'=>$id
        ]);
        return $this->LoadById($id);
    }
}

if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new UserLogins();
}

?>
<?php


class Users extends clsModel{
    public $table_name = "Users";
    public $fields = [
        [
            'Field'=>"id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>"auto_increment"
        ],[
            'Field'=>"username",
            'Type'=>"varchar(50)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"password",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"level",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"1",
            'Extra'=>""
        ],[
            'Field'=>"bedroom_id",
            'Type'=>"int(11)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"bedtime",
            'Type'=>"time",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"awake_time",
            'Type'=>"time",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"created",
            'Type'=>"datetime",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"current_timestamp()",
            'Extra'=>""
        ],[
            'Field'=>"last_login",
            'Type'=>"datetime",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"current_timestamp()",
            'Extra'=>"on update current_timestamp()"
        ]
    ];

    public function Ping($user_id){
        $this->Save(['last_login'=>date("Y-m-d H:i:s")],['id'=>$user_id]);
    }
    public function Create($username,$password,$level = 1){
        $this->Save(['username'=>$username,'password'=>$password,'level'=>$level]);
        return $this->GetUser($username);
    }
    public function GetUser($username){
        return $this->LoadWhere(['username'=>$username]);
    }
    public function GetUserWithPassword($username,$password){
        return $this->LoadWhere(['username'=>$username,'password'=>$password]);
    }
    public function CreateServerUser($username,$mac_address){
        return $this->Create($username,$mac_address,3);
    }
    public function GetServerUser($username,$mac_address){
        return $this->GetUserWithPassword($username,$mac_address);
    }
}

if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new Users();
}

/*
function PingUser($user_id){
    $time = date("Y-m-d H:i:s");
    clsDB::$db_g->safe_update("users",["last_login"=>$time],['id'=>$user_id]);
}

function PingUserLogin($login_id){
    $time = date("Y-m-d H:i:s");
    clsDB::$db_g->safe_update("user_login",["modified"=>$time],['id'=>$login_id]);
}

function GetUserName($username){
    $user = clsDB::$db_g->select("SELECT * FROM `users` WHERE `username` = '$username';");
    if(count($user)){
        return $user[0];
    }
    return NULL;
}
function CreateUser($username,$password){
    $id = clsDB::$db_g->safe_insert("users",["username"=>$username,"password"=>$password]);
    return GetUserById($id);
}

function GetUserById($user_id){
    $user = clsDB::$db_g->select("SELECT * FROM `users` WHERE `id` = '$user_id';");
    if(count($user)){
        return $user[0];
    }
    return NULL;
}

function GetLoginUserById($id){
    $login = clsDB::$db_g->select("SELECT * FROM `user_login` WHERE `id` = '$id';");
    if(count($login)){
        return $login[0];
    }
    return NULL;
}

function GetAllLoginUserById($id){
    return clsDB::$db_g->select("SELECT * FROM `user_login` WHERE `id` = '$id';");
}
function GetLoginUserByIP($ip){
    $login = clsDB::$db_g->select("SELECT * FROM `user_login` WHERE `ip` = '$ip' ORDER BY `modified` DESC LIMIT 1;");
    if(count($login)){
        return $login[0];
    }
    return NULL;
}
function GetLoginUserByToken($token){
    $login = clsDB::$db_g->select("SELECT * FROM `user_login` WHERE `token` = '$token' ORDER BY `modified` DESC LIMIT 1;");
    if(count($login)){
        return $login[0];
    }
    return NULL;
}
function CreateAnonLoginSession($ip){
    $id = clsDB::$db_g->safe_insert("user_login",[
        "user_id" => 0,
        "ip" => $ip,
        "token" => ""
    ]);
    return GetLoginUserById($id);
}
function LoginAnonUser($user_id,$login_id,$token){
    clsDB::$db_g->safe_update("user_login",['user_id'=>$user_id,"token"=>$token],['id'=>$login_id]);
    return GetLoginUserById($login_id);
}
function LogoutUserSession($id){
    clsDB::$db_g->safe_update("user_login",["token"=>""],['id'=>$id]);
    return GetLoginUserById($id);
}
function CreateServerUser($name,$mac_address){
    $user = GetUserName($name);
    if(is_null($user)){
        $id = clsDB::$db_g->safe_insert("users",["username"=>$name,"password"=>$mac_address,"level"=>3]);
    }
    return GetUserById($id);
}
function GetServerUser($name,$mac_address){
    $user = clsDB::$db_g->select("SELECT * FROM `users` WHERE `password` = '$mac_address' LIMIT 1;");
    if(count($user)){
        return $user[0];
    }
    $user = GetUserName($name);
    if(count($user)){
        return $user;
    }
    return null;
}*/
?>
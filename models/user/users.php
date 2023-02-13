<?php

/**
 * users model
 */
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
    /**
     * save the user
     * @param array $user the data array for a user record
     * @return array save report
     */
    public static function SaveUser($user){
        $instance = new Users();
        $user = $instance->CleanData($user);
        $exists = $instance->LoadById($user['id']);
        if(is_null($exists)){
            return $instance->Save($user);
        } else {
            return $instance->Save($user,['id'=>$user['id']]);
        }
    }
    /**
     * get all the users
     * @return array array of all the users
     */
    public static function AllUsers(){
        $instance = new Users();
        return $instance->LoadAll();
    }
    /**
     * get a user
     * @param int $user_id the user id
     * @return array array of all the users
     */
    public static function UserId($user_id){
        $instance = new Users();
        return $instance->LoadWhere(['id'=>$user_id]);
    }
    /**
     * get all the users with a level over 3.
     * @return array array of users that live here
     */
    public static function Residence(){
        $instance = new Users();
        return $instance->LoadFieldAfter("level",3);
    }
    /**
     * ping user... ie they're currently online
     * @param int $user_id the user's id
     */
    public function Ping($user_id){
        $this->Save(['last_login'=>date("Y-m-d H:i:s")],['id'=>$user_id]);
    }
    /**
     * create a new user
     * @param string $username the username (must not exist)
     * @param string $password the password
     * @param int $level the user level (default = 1)
     * @return array|null the user or null if username already exists
     */
    public function Create($username,$password,$level = 1){
        if(!is_null($this->GetUser($username))) return null;
        $this->Save(['username'=>$username,'password'=>$password,'level'=>$level]);
        return $this->GetUser($username);
    }
    /**
     * get a user with username
     * @param string $username the username
     * @return array the user data array
     */
    public function GetUser($username){
        return $this->LoadWhere(['username'=>$username]);
    }
    /**
     * get a user with username
     * @param string $username the username
     * @return array the user data array
     */
    public function GetUserId($user_id){
        return $this->LoadWhere(['id'=>$user_id]);
    }
    /**
     * get user with username and password
     * @param string $username the username
     * @param string $password the password
     * @return array|null the user data array or null if username and password don't match a user
     */
    public function GetUserWithPassword($username,$password){
        return $this->LoadWhere(['username'=>$username,'password'=>$password]);
    }
    /**
     * create a server user
     * @param string $username the server name
     * @param string $mac_address the server's mac address
     * @return array|null the user or null if username already exists
     */
    public function CreateServerUser($username,$mac_address){
        return $this->Create($username,$mac_address,3);
    }
    /**
     * get a server user
     * @param string $username the server name
     * @param string $mac_address the server's mac address
     * @return array|null the user or null if doesn't exists
     */
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
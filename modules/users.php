<?php

use PhpMyAdmin\Session;

/**
 * handles user sessions
 */
class UserSession {
    public static $user_session = null;
    public static $session = null;
    public static $user_logins = null;
    public static $users = null;
    public static $servers = null;
    /**
     * get the the static instance of this class UserSession::$user_session
     */
    public static function GetUserSession(){
        if(is_null(UserSession::$user_session)){
            UserSession::$user_session = new UserSession();
        }
        return UserSession::$user_session;
    }
    /**
     * get the session UserSession::$session
     */
    public static function GetUserSessionArray(){
        UserSession::GetUserSession();
        return UserSession::$session;
    }
    /**
     * gets the clean session data for user api
     */
    public static function CleanSessionData(){
        if(defined("SETUP_MODE")) {
            $session = [];
        } else {
            $session = UserSession::GetUserSessionArray();
        }
        if(!is_null($session['user'])){
            $session['user']['password'] = "[redacted]";
        } else {
            $session['user'] = ['id'=>0,'username'=>"guest",'level'=>0,'verified'=>0];
        }
        if(!is_null($session['token']) && $session['token'] != ""){
            $session['user']['verified'] = 1;
        } else {
            $session['user']['verified'] = 0;
        }
        return $session;
    }    
    /**
    * checks that the user level is equal to or above a threshold
    */
    public static function UserLevelCheck($level = 3){
        $session = UserSession::GetUserSessionArray();
        if(!is_null($session['user']) && $session['user']['level'] >= $level) return true;
        return false;
    }
    /**
    * checks that the user level is equal to or above a threshold
    */
    public static function UserIsServer(){
        $session = UserSession::GetUserSessionArray();
        if(!is_null($session['user']) && $session['user']['level'] == 3) return true;
        return false;
    }
    /** 
     * constructor sets up models and sets up session stuff
     */
    public function __construct()
    {
        $this->SetupModels();
        if(is_null(UserSession::$session)){
            UserSession::$session = UserSession::$user_logins->CreateAnonLoginSession($this->UserIpAddress());
        }
        UserSession::$session = $this->LoginServer(UserSession::$session);
        if(UserSession::$session['user_id'] == 0 && !Servers::IsMain()){
            // attempt a remote login
            UserSession::$session = $this->CheckMainForLogin(UserSession::$session);
            // make sure that the user exist locally?
            //UserSession::$session['user_id']['debug'] = "hello?";
        }
        if(UserSession::$session['user_id'] != 0 && (is_null(UserSession::$session['user']) || UserSession::$session['user']['id'] == 0)){
            UserSync::pull();
        }
        if(UserSession::$session['user_id'] != 0 && is_null(UserSession::$session['user'])){
            UserSession::$session['user'] = UserSession::$users->LoadById(UserSession::$session['user_id']);
            UserSession::$users->Ping(UserSession::$session['user_id']);
        }
        UserSession::$user_logins->Ping(UserSession::$session['id']);
        return UserSession::$session;
    }
    /**
     * sets up the static models and whatnot and tries to load the user session by ip from the database
     */
    private function SetupModels(){
        if(is_null(UserSession::$user_logins)){
            UserSession::$user_logins = new UserLogins();
        }
        if(is_null(UserSession::$users)){
            UserSession::$users = new Users();
        }
        if(is_null(UserSession::$servers)){
            UserSession::$servers = new Servers();
        }
        if(is_null(UserSession::$session)){
            UserSession::$session = UserSession::$user_logins->LoadByIp($this->UserIpAddress());
        }
    }
    /**
     * get the user ip address
     */
    public function UserIpAddress(){
        return $_SERVER['REMOTE_ADDR'];
    }
    /**
     * login a server by it's ip address
     */
    private function LoginServer($session){
        $server = UserSession::$servers->LoadByUrl($this->UserIpAddress());
        if(!is_null($server)){
            $session['user'] = UserSession::$users->GetServerUser($server['name'],$server['mac_address']);
            if(is_null($session['user'])){
                $session['user'] = UserSession::$users->CreateServerUser($server['name'],$server['mac_address']);
                $session['user_id'] = $session['user']['id'];
            }
            UserSession::$user_logins->LoginAnonUser($session['user']['id'],$session['id'],$server['mac_address']);
        }
        UserSession::$users->Ping($session['user_id']);
        return $session;
    }
    /**
     * login with a username and password
     * @param string $username
     * @param string $password
     */
    public function LoginUserSession($username,$password){
        $user = UserSession::$users->GetUser($username);
        $session = UserSession::$session;
        if(!is_null($user) && $user['password'] == $this->PasswordHash($username,$password)){
            $session = UserSession::$user_logins->LoginAnonUser($user['id'],$session['id'],$this->CreateToken($this->UserIpAddress()));
            $session['user'] = $user;
        } else {
            $session['user'] = null;
            if(is_null($user)){
                $session['login_error'] = "username [$username] not found";
            } else {
                $session['login_error'] = "password doesn't match";
            }
        }
        return $session;
    }
    /**
     * create user with username and password
     * @param string $username
     * @param string $password
     */
    public function SignupUserSession($username,$password){
        $user = UserSession::$users->GetUser($username);
        $session = UserSession::$session;
        if(is_null($user)){
            $user = UserSession::$users->Create($username,$this->PasswordHash($username,$password));
            $session = UserSession::$user_logins->LoginAnonUser($user['id'],$session['id'],$this->CreateToken($this->UserIpAddress()));
            $session['user'] = $user;    
        } else {
            $session['signup_error'] = "username [$username] already exists";
        }
        return $session;
    }
    /**
     * utility functions
     */
    /**
     * hash a password
     * @param string $username
     * @param string $password
     */
    public function PasswordHash($username,$password){
        $default = "";
        if(Servers::IsMain()) $default = md5(date("Y-m-d H:i:s"));
        $salt = Settings::LoadSettingsVar("salt",$default);
        return md5($username.$salt.$password.$salt.$username);
    }
    /**
     * create a new user token and save it as a cookie
     * @param string $ip the user's ip
     * @return string the user token
     */
    public function CreateToken($ip){
        $token = md5($ip.time());
        setcookie('user_token',$token,time()+(86400 * 30),"/");
        return $token;
    }
    /**
     * clear the user token cookie
     */
    public function ClearToken(){
        setcookie('user_token',"",time()-3600,"/");
    }
    /**
     * logout user
     */
    public function LogoutUserSession(){
        UserSession::$session = UserSession::$user_logins->LogoutUserSession(UserSession::$session['id']);
    }
    /**
     * check the main hub for an existing login
     * @param array a session data array
     * @return array the session data array... maybe it's now logged in a use
     */
    public function CheckMainForLogin($session){
        $hub = Servers::GetMain();
        $url = "http://".$hub['url']."/api/user/login/?ip=".$this->UserIpAddress();
        $info = file_get_contents($url);
        $data = json_decode($info,true);
        if($data['user_login']['user_id'] != 0){
            $session = UserSession::$user_logins->LoginAnonUser($data['user_login']['user_id'],$session['id'],$this->CreateToken($this->UserIpAddress()));

        }
        return $session;
    }
}
/*
function UserSession(){
    $session = GetLoginUserByIP(UserIpAddress());
    if(is_null($session)){
        $session = CreateAnonLoginSession(UserIpAddress());
    }
    $session = LoginServer($session);
    if($session['user_id'] != 0 && is_null($session['user'])){
        $session['user'] = GetUserById($session['user_id']);
        PingUser($session['user_id']);
    }
    PingUserLogin($session['id']);
    return $session;
}
// login
function LoginUserSession($session,$username,$password){
    $user = GetUserName($username);
    if(!is_null($user) && $user['password'] == PasswordHash($username,$password)){
        $session = LoginAnonUser($user['id'],$session['id'],CreateToken(UserIpAddress()));
        $session['user'] = $user;
    } else {
        $session['user'] = null;
        if(is_null($user)){
            $session['login_error'] = "username [$username] not found";
        } else {
            $session['login_error'] = "password doesn't match";
        }
    }
    return $session;
}
// signup
function SignupUserSession($session,$username,$password){
    $user = GetUserName($username);
    if(is_null($user)){
        $user = CreateUser($username,PasswordHash($username,$password));
        $session = LoginAnonUser($user['id'],$session['id'],CreateToken(UserIpAddress()));
        $session['user'] = $user;    
    } else {
        $session['signup_error'] = "username [$username] already exists";
    }
    return $session;
}
function LoginServer($session){
    $server = ServerIP(UserIpAddress());
    if(count($server)){
        $session['user'] = GetServerUser($server['name'],$server['mac_address']);
        if(is_null($session['user'])){
            $session['user'] = CreateServerUser($server['name'],$server['mac_address']);
            $session['user_id'] = $session['user']['id'];
        }
        LoginAnonUser($session['user']['id'],$session['id'],$server['mac_address']);
    }
    PingUser($session['user_id']);
    return $session;
}
// logout -> LogoutUserSession($id) in model does this.


// utility functions
// hash a password
function PasswordHash($username,$password){
    return md5($username.$password.$username);
}
// remove password hash and set if it's verified.
function CleanSessionData($session){
    if(!is_null($session['user'])){
        $session['user']['password'] = "[redacted]";
    } else {
        $session['user'] = ['id'=>0,'username'=>"guest",'level'=>0,'verified'=>0];
    }
    if(!is_null($session['token']) && $session['token'] != ""){
        $session['user']['verified'] = 1;
    } else {
        $session['user']['verified'] = 0;
    }
    return $session;
}
// get the user ip address
function UserIpAddress(){
    return $_SERVER['REMOTE_ADDR'];
}
// get the user token
function UserToken(){
    if(isset($_COOKIE['user_token'])){
        return $_COOKIE['user_token'];
    }
    if(isset($_GET['user_token'])){
        return $_GET['user_token'];
    }
    return NULL;
}
// create a new user token
function CreateToken($ip){
    $token = md5($ip.time());
    setcookie('user_token',$token,time()+(86400 * 30),"/");
    return $token;
}
// clear the user token cookie
function ClearToken(){
    setcookie('user_token',"",time()-3600,"/");
}





/*
// old stuff i'm probably gonna get rid of........
function LoginUserIP($ip){
    $user = GetUserIP($ip);
    // need to create a new user
    if(is_null($user)){
        // check to see if it's a known server
        $server = ServerIP($ip);
        if(!is_null($server)){
            // login server
            $token = CreateToken($ip);
            $user = CreateNamedUser($server['name'],md5($server['mac_address']),$ip,$token);
            $user = UpdateUserLevel($ip,3);
            $user['verified'] = 1;
        } else {
            // create anon user
            $user = CreateAnonUser($ip);
            $user['verified'] = 0;
        }
    }
    $user['verified'] = 0;
    return $user;
}
function LoginUserToken($token,$user_ip){
    //echo "[LoginUserToken:$token]<br>";
    $user = GetUserToken($token);
    if(is_null($user)){
        if(is_null($user_ip)){
            return NULL;
        }
        $user = LoginUserIP($user_ip);
    } else {
        if(is_null($user['username']) || $user['username'] == ""){
            $user['verified'] = 0;
        } else {
            $user['verified'] = 1;
        }
    }
    return $user;
}
function LoginUser($username,$password,$user_ip){
    //echo "[LoginUser:$username]";
    $user = GetUserName($username);
    if(is_null($user)){
        return NULL;
    }
    $user['verified'] = 0;
    if($user['password'] == md5($username.$password)){
        $token = CreateToken($user_ip);
        $user = UpdateUser($username,$user_ip,$token);
        $user['verified'] = 1;
    }
    $user['password'] = null;
    return $user;
}
function CreateNewUser($username,$password,$ip){
    $user = GetUserIP($ip);
    if(!is_null($user)){
        // username doesn't already exist
        if(is_null($user['username'])){
            NameAnonUser($username,md5($username.$password),$ip);
            $token = CreateToken($ip);
            $user = UpdateUserToken($ip,$token);
            $user = UpdateUserLevel($ip,1);
            $user['password'] = null;
            return $user;
        }
    }
    return null;
}
/* do global user handling */
/*
function GetUserObject(){
    $user_ip = $_SERVER['REMOTE_ADDR'];
    //echo "ip:$user_ip<br>";
    if(isset($_COOKIE['user_token'])){
        $user_token = $_COOKIE['user_token'];
    }
    if(isset($_GET['user_token'])){
        $user_token = $_GET['user_token'];
    }
    //echo "token:$user_token<br>";
    if(isset($user_token) && !is_null($user_token) && $user_token != ""){
        $user = LoginUserToken($user_token,$user_ip);
    }
    if(!isset($user) || is_null($user)){
        $user = LoginUserIP($user_ip);
    }
    if(isset($user) && !is_null($user)){
        $user['password'] = null;
    }
    return $user;
}
*/
?>
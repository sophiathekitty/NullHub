<?php
class UserSession {
    public static $user_session = null;
    public static $session = null;
    public static $user_logins = null;
    public static $users = null;
    public static $servers = null;

    public static function GetUserSession(){
        if(is_null(UserSession::$user_session)){
            UserSession::$user_session = new UserSession();
        }
        return UserSession::$user_session;
    }
    public static function GetUserSessionArray(){
        UserSession::GetUserSession();
        return UserSession::$session;
    }

    public function __construct()
    {
        $this->SetupModels();
        if(is_null(UserSession::$session)){
            UserSession::$session = UserSession::$user_logins->CreateAnonLoginSession($this->UserIpAddress());
        }
        UserSession::$session = $this->LoginServer(UserSession::$session);
        if(UserSession::$session['user_id'] != 0 && is_null(UserSession::$session['user'])){
            UserSession::$session['user'] = UserSession::$users->LoadById(UserSession::$session['user_id']);
            UserSession::$users->Ping(UserSession::$session['user_id']);
        }
        UserSession::$user_logins->Ping(UserSession::$session['id']);
        return UserSession::$session;
    }
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
    // get the user ip address
    public function UserIpAddress(){
        return $_SERVER['REMOTE_ADDR'];
    }

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

    // login
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
    // signup
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

    // utility functions
    // hash a password
    public function PasswordHash($username,$password){
        return md5($username.$password.$username);
    }

    // create a new user token
    public function CreateToken($ip){
        $token = md5($ip.time());
        setcookie('user_token',$token,time()+(86400 * 30),"/");
        return $token;
    }
    // clear the user token cookie
    public function ClearToken(){
        setcookie('user_token',"",time()-3600,"/");
    }
    public function LogoutUserSession(){
        UserSession::$session = UserSession::$user_logins->LogoutUserSession(UserSession::$session['id']);
    }
    public static function CleanSessionData(){
        $session = UserSession::GetUserSessionArray();
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
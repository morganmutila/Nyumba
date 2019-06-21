<?php


class Session {

    private $logged_in = false;    

    public  $user_id;
    public  $location;
    public  $message;

    public function __construct(){      
        session_name("nyumbayanga");  
        session_start();
        $this->checkMessage();
        $this->checkLogin();
        $this->checkUserLocation();
    }

    private function checkMessage(){
        // Is there a session message stored
        if (self::exists('MESSAGE')) {
            $this->message = self::get('MESSAGE');
            self::delete('MESSAGE');
        }else{
            $this->message = "";
        }
    }

    private function checkLogin(){
        if(self::exists('USER_ID')){
            $this->user_id = self::get('USER_ID');
            $this->logged_in = true;
        }
        elseif(Cookie::exists(Config::get('remember/cookie_name')) && !self::exists('USER_ID')){

            $hashkey = Cookie::get(Config::get('remember/cookie_name'));

            $sql = "SELECT * FROM remember WHERE hashkey = ? LIMIT 1";
            $found_user = Remember::findFirst($sql, [$hashkey]);
            if($found_user){
                $user = User::findById($found_user->user_id);
                $this->login($user);
            }
        }
        else{
            unset($this->user_id);
            $this->logged_in = false;
        }
    }

    private function checkUserLocation(){
        if(self::exists('LOCATION')){
            $this->location = self::get('LOCATION');
        }else{
            $this->location = null;
        }
    }

    public function message($msg=""){
        if(!empty($msg)){
            self::put('MESSAGE', $msg);
        }else{
            return htmlentities($this->message);
        }
    }

    public static function flash($name, $message = ''){
        if(self::exists($name)){
            $session = self::get($name);
            self::delete($name);
            return $session;
        }else{
            self::put($name, $message);
        }
    }

    public function isLoggedIn(){
        return $this->logged_in;
    }

    public function login($user, $remember_me = false){
        // The database will take care of finding user based on the username/password
        if($user){
            self::put('USER_ID', $user->id);
            $this->user_id = self::get('USER_ID');
            // Put the user ID in the cookie as well
            if($remember_me){
                Cookie::rememberMe($user->id);
            }

            $user->last_login = text_to_datetime(Config::get('mysql_date_time_format'));
            $user->save();
            $this->logged_in = true;
        }
    }

    public function logout(){
        self::delete('USER_ID');
        unset($this->user_id);
        Cookie::delete(Config::get('remember/cookie_name'));
        $this->logged_in = false;
    }

    public static function generateToken(){
        return $_SESSION['TOKEN'] = md5(uniqid());
    }

    public static function checkToken($token){
        if(isset($_SESSION['TOKEN']) && $token === $_SESSION['TOKEN']){
            unset($_SESSION['TOKEN']);
            return true;
        }else{
            return false;
        }
    }

    // Helper Static functions for Sessions
    public static function exists($name){
        return (isset($_SESSION[$name])) ? true : false;
    }

    public static function put($name, $value){
        return $_SESSION[$name] = $value;
    }

    public static function get($name){
        return $_SESSION[$name];
    }

    public static function delete($name){
        if (self::exists($name)) {
            unset($_SESSION[$name]);
        }
    }
}
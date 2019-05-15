<?php


class Session {

    private $logged_in = false;    

    public  $user_id;
    public  $user_location;
    public  $message;

    public function __construct(){        
        session_start();
        $this->checkMessage();
        $this->checkLogin();
        $this->checkUserLocation();
    }

    private function checkMessage(){
        // Is there a session message stored
        if (self::exists('message')) {
            $this->message = self::get('message');
            self::delete('message');
        }else{
            $this->message = "";
        }
    }

    private function checkLogin(){
        if(self::exists('user_id')){
            $this->user_id = self::get('user_id');
            $this->logged_in = true;
        }
        else{
            unset($this->user_id);
            $this->logged_in = false;
        }
    }

    private function checkUserLocation(){

    }

    public function message($msg=""){
        if(!empty($msg)){
            self::put('message', $msg);
        }else{
            return htmlentities($this->message);
        }
    }

    public function isLoggedIn(){
        return $this->logged_in;
    }

    public function login($user){
        // The database will take care of finding user based on the username/password
        if($user){
            $this->user_id = $_SESSION['user_id'] = $user->id;
            // Put the user ID in the cookie as well
            //Cookie::put();
            $user->last_login = text_to_datetime(Config::get('mysql_date_time_format'));
            $user->save();
            $this->logged_in = true;
        }
    }

    public function logout(){
        unset($_SESSION['user_id']);
        unset($this->user_id);
        $this->logged_in = false;
    }

    public static function generateToken(){
        return $_SESSION['token'] = md5(uniqid());
    }

    public static function checkToken($token){
        if(isset($_SESSION['token']) && $token === $_SESSION['token']){
            unset($_SESSION['token']);
            return true;
        }else{
            return false;
        }
    }

    // Helper functions for Sessions
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

    public static function flash($name, $string = ''){
        if (self::exists($name)) {
            $session = self::get($name);
            self::delete($name);
            return $session;
        } else {
            self::put($name, $string);
        }
    }
}
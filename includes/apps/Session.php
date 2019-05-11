<?php


class Session {

    private  $logged_in = false;    
    public   $user_id;
    public   $message;

    public function __construct(){        
        session_start();
        $this->checkMessage();
        $this->checkLogin();
    }

    private function checkMessage(){
        // Is there a session message stored
        if (isset($_SESSION['message'])) {
            $this->message = $_SESSION['message'];
            unset($_SESSION['message']);
        }else{
            $this->message = "";
        }
    }

    public function message($msg=""){
        if(!empty($msg)){
            $_SESSION['message'] = $msg;
        }else{
            return htmlentities($this->message);
        }
    }

    public function isLoggedIn(){
        return $this->logged_in;
    }

    private function checkLogin(){
        if(isset($_SESSION['user_id'])){
            $this->user_id = $_SESSION['user_id'];
            $this->logged_in = true;
        }
        else{
            unset($this->user_id);
            $this->logged_in = false;
        }
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

}
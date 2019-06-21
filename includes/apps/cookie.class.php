<?php

class Cookie {

    public static function rememberMe($user_id=0){
        $remember_key  = md5(time());

        $sql = "SELECT * FROM remember WHERE user_id = ? LIMIT 1";
        
        $remember = Remember::findFirst($sql, [$user_id]);

        if (!$remember) {           
            $remember = new Remember;
            $remember->hashkey = $remember_key;
            $remember->user_id = $user_id;
            $remember->create();
        }
        else{
           $remember_key = $remember->hashkey;
        }

        // Put the user's hashkey into the COOKIE
        self::put(Config::get('remember/cookie_name'), $remember_key, Config::get('remember/cookie_expiry'));
    }

    public static function exists($name){
        return (isset($_COOKIE[$name])) ? true : false;
    }

    public static function get ($name){
        return $_COOKIE[$name];
    }

    public static function put($name, $value, $expiry){
        if(setcookie($name, $value, time() + $expiry, '/')){
            return true;
        }

        return false;
    }

    public static function delete($name){
        self::put($name, '', time() - 1);
    }
} 
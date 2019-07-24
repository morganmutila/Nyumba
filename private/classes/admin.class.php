<?php

class Admin extends User {

	protected static $table_name = "admin";
    protected static $columns = ['id','city_id', 'username', 'first_name', 'last_name', 'email',
             'phone', 'joined', 'group_id', 'location_id', 'status', 'password','job_title','avatar', 'last_login','gender'];

 	// Class attributes
    public $id;
    public $group_id;
    public $city_id;
    public $username;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $joined;
    public $location_id;
    public $status;
    public $password;
    public $last_login;
    public $job_title;  
    public $gender;
    public $avatar;            
 	

    public static function authenticate($username = "", $password = "", $email="", $remember_me=false){
        if($username){            
            $sql  = "SELECT * FROM ".self::$table_name;
            $sql .= " WHERE username = ? OR email= ? LIMIT 1";

            // Get the user data
            $found_user = self::findBySql($sql, array($username, $email)); 
        
            if($found_user){
                $found_user = array_shift($found_user);
                if(password_verify($password, $found_user->password)){ 
                    return $found_user;
                } else{
                    return false;
                }                
            }
        }
    }
} 
<?php

class User extends DBO{

    protected static $table_name = "users";
    protected static $columns = ['id','username', 'first_name', 'last_name', 'email',
             'phone', 'joined', 'group_id', 'location_id', 'status', 'password', 'last_login', 'ip'];

    // Class attributes
    public $id;
    public $username;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $joined;
    public $group_id;
    public $location_id;
    public $status;
    public $password;
    public $last_login;
    public $ip;


    public function __construct($args=[]) {
        $this->username     = $args['username'] ?? '';
        $this->first_name   = $args['first_name'] ?? '';
        $this->last_name    = $args['last_name'] ?? '';
        $this->email        = $args['email'] ?? '';
        $this->phone        = $args['phone'] ?? '';
        $this->joined       = $args['joined'] ?? '0';
        $this->group_id     = $args['group_id'] ?? 0;
        $this->location_id  = $args['location_id'] ?? 0;
        $this->status       = $args['status'] ?? 0;
        $this->password     = $args['password'] ?? '';
        $this->last_login   = $args['last_login'] ?? '0';
        $this->ip           = $args['ip'] ?? '';
    }

    // Class methods
    public static function authenticate($username = "", $password = "", $email="", $phone="", $remember_me=false){
        if($username){            
            $sql  = "SELECT * FROM ".self::$table_name;
            $sql .= " WHERE username = ? OR email= ? OR phone = ? LIMIT 1";

            // Get the user data
            $found_user = self::findBySql($sql, array($username, $email, $phone)); 
        
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


    public function fullName(){
        if(isset($this->first_name) && isset($this->last_name)){
            return $this->first_name . " " . $this->last_name;
        }else{
            return "";
        }
    }

    public function userFolder($username=""){   
        if(Config::get('allow_user_folders') == true){     
            if (!file_exists(SITE_ROOT .DS. 'users' .DS. $username)) {
                mkdir(SITE_ROOT .DS. 'users' .DS. strtolower($username), 0777, true);
                return true;
            }else{
                return false;
            }
        }
    }

    public function location(){ 
        $location = Location::findLocationOn($this->location_id);
        return ucwords($location);
    }

    public function propertyCount(){
        DB::getInstance()->direct_query("SELECT COUNT(*) FROM property WHERE user_id =".$this->id);
        $count = DB::getInstance()->result();
        return array_shift($count);
    }

    public function savedProperty($property_id=0){
        if($property_id != 0){
            $sql = "SELECT id FROM saved WHERE property_id = ? AND user_id = ?";
            DB::getInstance()->query($sql, array($property_id, $this->id));
            if(DB::getInstance()->count()){
                return true;
            }
        }
    }

} 
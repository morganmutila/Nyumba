<?php

//The Config class ***********************************************************************************
class Config {

    public static function get($path = null){
        if(isset($path)){

            $config = $GLOBALS['config'];
            $path = explode('/', $path);

            foreach($path as $bit){
                if(isset($config[$bit])){
                    $config = $config[$bit];
                }
            }

            return $config;
        }

        return false;
    }
} 


//The Input class ************************************************************************************
class Input {

    public static function exists($type = 'post'){
        switch($type){
            case 'post':
                return (!empty($_POST))? true: false;
                break;
            case 'get':
                return (!empty($_GET))? true: false;
                break;
            default:
                return false;
                break;
        }
    }

    public static function get($item){

        if(isset($_POST[$item])){
            return $_POST[$item];
        }elseif(isset($_GET[$item])){
            return $_GET[$item];
        }

        return false;
    }
} 


//The Redirect class *********************************************************************************
class Redirect {

    private static $base_url = "index.php";

    public static function to($location = null){
        if($location){
            if(is_numeric($location)){
                switch($location){
                    case 404:
                        header('HTTP/1.0 404 Not Found');
                        include 'includes/404.php';
                        exit();
                    break;
                }
            }

            header('Location: '.$location);
            exit();
        }
    }

    public static function prevPage(){
        if(isset($_SERVER['HTTP_REFERER']))
        return self::to($_SERVER['HTTP_REFERER']);
    }

    public static function home(){
        return self::to(self::$base_url);
    }

    public static function authPage(){
        return self::to("login.php");
    }
} 

//The URL class *********************************************************************************
class URL {

    private static $base_url = "index";
    
    function link($string=""){
        if(!empty($string))                               
            return  rawurldecode($string).".php";
    }
}


//The Pagination class *******************************************************************************
class Pagination {
    
  public $current_page;
  public $per_page;
  public $total_count;

  public function __construct($page=1, $per_page=20, $total_count=0){
    $this->current_page = (int)$page;
    $this->per_page = (int)$per_page;
    $this->total_count = (int)$total_count;
  }

  public function offset() {
    // Assuming 20 items per page:
    // page 1 has an offset of 0    (1-1) * 20
    // page 2 has an offset of 20   (2-1) * 20
    //   in other words, page 2 starts with item 21
    return ($this->current_page - 1) * $this->per_page;
  }

  public function total_pages() {
    return ceil($this->total_count/$this->per_page);
    }
    
  public function previous_page() {
    return $this->current_page - 1;
  }
  
  public function next_page() {
    return $this->current_page + 1;
  }

    public function has_previous_page() {
        return $this->previous_page() >= 1 ? true : false;
    }

    public function has_next_page() {
        return $this->next_page() <= $this->total_pages() ? true : false;
    }
}


//The Unique key Validation class *******************************************************************************
require PACKAGE_PATH;
use Rakit\Validation\Rule;

class UniqueRule extends Rule {

    protected $message = ":attribute :value has been used";    
    protected $fillableParams = ['table', 'column', 'except'];
    
    public function check($value): bool {
        // make sure required parameters exists
        $this->requireParameters(['table', 'column']);
    
        // getting parameters
        $column = $this->parameter('column');
        $table  = $this->parameter('table');
        $except = $this->parameter('except');
    
        if ($except AND $except == $value) {
            return true;
        }
    
        // do query
        $sql    = "SELECT COUNT(*) AS count FROM `{$table}` WHERE `{$column}` = ?";
        DB::getInstance()->query($sql, [$value]);
        $result = DB::getInstance()->result("count");
   
        // true for valid, false for invalid
        return intval($result) === 0;
    }
}

//Add this unique Rule to the to the Validator

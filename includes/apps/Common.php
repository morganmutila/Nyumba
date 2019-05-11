<?php

//The Config class *******************************************************************************
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


//The Input class *******************************************************************************
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
            return ($_GET[$item]);
        }

        return '';
    }
} 


//The Redirect class *******************************************************************************
class Redirect {
    public static function to($location = null){
        if($location){
            if(is_numeric($location)){
                switch($location){
                    case 404:
                        header('HTTP/1.0 404 Not Found');
                        include 'includes/error404.php';
                        exit();
                    break;
                }
            }

            header('Location: '.$location);
            exit();
        }
    }
} 


//The Cookie class *******************************************************************************
class Cookie {

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


//The Validation class *******************************************************************************
class Validation {

    private $passed = false,
            $errors = array();

    public function check($source, $items = array()){
        foreach($items as $item => $rules){
            foreach($rules as $rule => $rule_value){

                $value = trim($source[$item]);
                $item  = escape($item);

                if($rule === 'required' && empty($value)){
                    $this->addError($item, "{$item} cannot be empty");
                }else if(!empty($value)){
                    switch($rule){
                        case 'min':
                            if(strlen($value) < $rule_value){
                                $this->addError($item, "{$item} must be a minimum of {$rule_value} characters");
                            }
                            break;
                        case 'max':
                            if(strlen($value) > $rule_value){
                                $this->addError($item, "{$item} should not exceed {$rule_value} characters");
                            }
                            break;
                        case 'matches':
                            if($value != $source[$rule_value]){
                                $this->addError($item, "{$rule_value} must match {$item}");
                            }
                            break;
                        case 'unique':
                            $sql    = "SELECT ".$item." FROM ".$rule_value." WHERE ".$item." = ?";
                            $params = array($value);
                            $check = DB::getInstance()->query($sql, $params);

                            if(DB::getInstance()->count() > 0){
                                $this->addError($item, "'$value' already exists");
                            }
                            break;

                        case 'number_only':
                            if(!preg_match("/^[0-9]+$/", $value)){
                                $this->addError($item, "{$item} has Invalid format, only numbers are allowed");
                            }
                            break;

                        case 'text_only':
                            if (!is_numeric((int)$value) && filter_var($value, FILTER_SANITIZE_STRING)){
                                $this->addError($item, "{$item} has invalid format, no numbers are allowed");
                            }
                            break;    

                        case 'valid_email':
                            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $this->addError($item, "Invalid email address '$value'");
                            }
                            break; 
                        case 'value_in_array':
                            if(!in_array($value, )){
                                $this->addError($item, "Invalid option". $value);
                            }   
                            break;
                    }
                }
            }
        }

        if(empty($this->errors)){
            $this->passed = true;
        }

        return $this;
    }

    private function addError($field, $error){
        $clean_field = escape($field);
        $clean_error = ucfirst(str_ireplace('_', ' ', $error));
        $this->errors[$clean_field] = $clean_error;
    }

    public function errors(){
        return $this->errors;
    }

    public function passed(){
        return $this->passed;
    }
} 
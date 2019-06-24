<?php

class Admin extends User {

	protected static $table_name = "admin";
    protected static $db_fields = ['id','city_id', 'username', 'first_name', 'last_name', 'email',
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
 	

 	public function __toString(){
        $output  =  isset($this->id)          ? "User ID: " .    $this->id ."<br>" : "";
        $output .=  isset($this->username)    ? "Username: " .   $this->username ."<br>" : "";
        $output .=  isset($this->id)          ? "Name: " .       $this->fullName() ."<br>" : "";
        $output .=  isset($this->joined)      ? "Job Title: " .  $this->job_title ."<br>" : "";
        return $output;
    }
} 
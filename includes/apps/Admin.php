<?php

class Admin extends User {
 	
 	function __construct() 	{
 		# code...
 	}

 	public function __toString(){
        $output  =  isset($this->id)          ? "User ID: " .    $this->id ."<br>" : "";
        $output .=  isset($this->username)    ? "Username: " .   $this->username ."<br>" : "";
        $output .=  isset($this->id)          ? "Name: " .       $this->fullName() ."<br>" : "";
        $output .=  isset($this->joined)      ? "Job Title: " .  $this->job_title ."<br>" : "";
        return $output;
    }
} 
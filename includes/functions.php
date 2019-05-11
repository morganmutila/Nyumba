<?php

// Auto load classes in case they have not been required
function __autoload($class_name){
    $class_name = ucfirst($class_name);
    $path = CLASS_PATH .DS. $class_name . ".php";
    if (file_exists($path)) {
        require($path);
    }
    else{
        die("The file {$class_name}.php can not be found");
    }    
}

function pre($value){
    echo '<pre>';
    print_r($value);
    echo '</pre>';
}

function varPre($value){
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
}

function escape($string){
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

function clean($str) {
    $str = @trim($str);
    if(get_magic_quotes_gpc()) {
        $str = stripslashes($str);
    }
    return $str;
}

function php_format_date($timeString){
    return date("j M Y" ,strtotime($timeString));
}

function text_to_datetime($format){
    return strftime($format, time());
}

function datetime_to_text($datetime="") {
  $unixdatetime = strtotime($datetime);
  return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}

function page_title($page_title=""){
    if(empty($page_title)  && !isset($page_title)){
        $page_title = "Nyumba Yanga · List your property";
    }
    else{
        $page_title = $page_title;
    }
    return $page_title;
}

function include_layout_template($template=""){
    global $page_title, $session, $user;
    include(SITE_ROOT.DS.'public'.DS.'layouts'.DS.$template);
}

function get_form_errors($errors=array()){
    $form_errors = array();
    if(!empty($errors)){
        foreach ($errors as $key => $error) {
            $form_errors[$key] = $error;
        }
        return join("<br>", $form_errors);     
    }
}

function output_message($message="") {
  if (!empty($message)) { 
    return "<p class=\"message\">{$message}</p>";
  } else {
    return "";
  }
}

function log_action($action, $message="") {
    $logfile = SITE_ROOT.DS.'logs'.DS.'log.txt';
    $new = file_exists($logfile) ? false : true;
  if($handle = fopen($logfile, 'a')) { // append
    $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
        $content = "{$timestamp} | {$action}: {$message}\n";
    fwrite($handle, $content);
    fclose($handle);
    if($new) { chmod($logfile, 0755); }
  } else {
    echo "Could not open log file for writing.";
  }
}

function create_form_select($name="", $count=null, $accept=""){
    if($count > 0){
        if($accept == "half_values"){
            $output = "<select name=\"{$name}\">";
                for($i=1; $i <= $count; $i = $i + 0.5){               
                    if ($i == $count) {
                        $output .= "<option value=\"$i\">$i+</option>";
                    }else{
                        $output .= "<option value=\"$i\">$i</option>";
                    }
                } 
            $output .= "</select>";
            return $output;
        }    
        else{
            $output = "<select name=\"{$name}\">";
                for($i=1; $i <= $count; $i++){               
                    if ($i == $count) {
                        $output .= "<option value=\"$i\">$i+</option>";
                    }else{
                        $output .= "<option value=\"$i\">$i</option>";
                    }
                } 
            $output .= "</select>";
            return $output;
        } 
    }
    return "";
}

function generate_form_select($name="", $empty_select=true, $key_values=array()){
    if(count($key_values)){
        $output = "<select name=\"{$name}\">";
            if ($empty_select) {
                $output .= "<option>Please select</option>";
            }
            foreach ($key_values as $key => $value) {
                $output .= "<option value=\"$value\" ";
                    if(Input::get($name) == $value){ 
                        $output .= "selected";
                    }
                $output .= ">".$key."</option>";
            }            
        $output .= "</select>";
        return $output;
    }
    return "";
}

function generate_form_checkbox($name="", $key_values=array()){
    $output = "";
    if(count($key_values)){        
        foreach ($key_values as $key => $value) {
            $output .= "<label><input type=\"checkbox\" name=\"{$name}\" value=\"{$value}\""; 
                if(Input::get($name) == $value){ 
                    $output .= "checked";
                }
            $output .="/>".$key."</label>";
        }            
        return $output;
    }
    return "";
}

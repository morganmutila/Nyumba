<?php

// Auto load classes in case they have not been required
spl_autoload_register(function($class_name){
    $class_name = ucfirst($class_name);
    $path = CLASS_PATH .DS. $class_name . ".php";
    if (file_exists($path)) {
        require($path);
    }
    else{
        die("The file {$class_name}.php can not be found");
    }   
});

function NY_SEARCH_ENGINE(){
    global $session;

    $html  = "<form action=\"search.php\" method=\"GET\" style=\"position:relative;\">";
    $html .= "    <input type=\"text\" name=\"q\" placeholder=\"Search location\" style=\"padding-right:7rem;border-radius: 4px;\">";
    if(isset($session->location)){
        $html .= "<div style=\"font-size:.9rem;position:absolute;right:0;top:0;padding:0 .6rem;border-left:1px solid #ddd;color: #bbb;height:65%;line-height:1.6rem;margin:2% 0;\">Ꝋ&nbsp;";            
        $html .= "<small>".Location::findLocationOn($session->location)."</small>";
        $html .= "</div>";   
    }     
    $html .= "</form>";
    return $html;
}

function pre($value){
    echo '<pre>';
    print_r($value);
    echo '</pre>';
}

function varpre($value){
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
}

function escape($string){
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

function new_listing($datetime){
    $datetime = new DateTime($datetime);
    $valid_for = new DateInterval(Config::get('new_listing'));

    $now = new DateTime();

    $expiry_date = clone $datetime;
    $expiry_date->add($valid_for);

    return $now < $expiry_date;
}

//********************************************************************
//Time functions
//********************************************************************
function time_ago($time){
    $formated_time = strtotime($time);
    $time_difference = time() - $formated_time;
 
    if( $time_difference < 1 ) { return 'less than 1 second ago'; }
    $condition = array( 
                12 * 30 * 24 * 60 * 60 =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
    );
 
    foreach( $condition as $secs => $str ){
        $d = $time_difference / $secs;
 
        if( $d >= 1 ){
            $t = round( $d );
            return $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
        }
    }
}

function text_to_datetime($format){
    return strftime($format, time());
}

function datetime_to_text($datetime="") {
  $unixdatetime = strtotime($datetime);
  return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}
//***********************************End Time functions

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
    include(INCLUDE_PATH .DS.'layouts'.DS.$template);
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

function output_message($message="",  $type="info") {
    $output = "";   
    if (!empty($message)) {    
        switch ($type) {
            case $type == 'success':
                $output .= "<p class=\"message-success\">".$message."</p>";
                break;
            
            case $type == 'warning':
                $output .= "<p class=\"message-warning\">".$message."</p>";
                break;

            case $type == 'info':
                $output .= "<p class=\"message-info\">".$message."</p>";
                break;  

            case $type == 'danger':
            $output .= "<p class=\"message-danger\">".$message."</p>";
            break;   
        }
    }    
    return $output;
}

function flash($name, $type="info"){
    $output = "";
    if(Session::exists($name)){        
        switch ($type) {
            case $type == 'success':
                $output .= "<p class=\"message-success\">".Session::flash($name)."</p>";
                break;
            
            case $type == 'warning':
                $output .= "<p class=\"message-warning\">".Session::flash($name)."</p>";
                break;

            case $type == 'info':
                $output .= "<p class=\"message-info\">".Session::flash($name)."</p>";
                break;  

            case $type == 'danger':
            $output .= "<p class=\"message-danger\">".Session::flash($name)."</p>";
            break;   
        }
    }    
    return $output;
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
        if($accept == "fractions"){
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
                $output .= "<option>Please select --</option>";
            }
            foreach ($key_values as $key => $value) {
                $output .= "<option value=\"$value\" ";
                    if(Input::get($name) == $value){ $output .= "selected"; }
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


//Money format
function amount_format($amount = '0', $symbol = 'K') {
    $amount = round($amount, 2);
    $sign = '';
    if ( substr($amount, 0, 1) == '-'){
        $sign = '-';
        $amount = substr($amount, 1);
    }
    if($symbol == " ") {        // If you want the format without any symbol then pass space, ie: " "
        $amount = $sign . number_format($amount, 0,'.','');
    } else {
        $amount = $sign . $symbol . number_format($amount, 0);
    }
    return $amount;
}

/*************for thumbnail display**********/
function thumb_image($imgsrc, $thumbsize = "100", $alt = "Image", $title = "Image" ) {
    if (file_exists($imgsrc)) {
        list($width, $height ) = getimagesize($imgsrc);
    
        $imgratio = $width/$height;
    if ( $imgratio > 1 ) {
        $newwidth  = $thumbsize;
        $newheight = $thumbsize/$imgratio;
    }else {
        $newheight = $thumbsize;
        $newwidth  = $thumbsize*$imgratio;
    }
     return '<img src="' . $imgsrc . '" width="' . $newwidth . '" height="' . $newheight . '"  alt="' . $alt . '" border="0" title="' . $title . '" >';
    }
    else {
        echo "No Image";
    }
    
 }

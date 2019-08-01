<?php if ( ! defined('SITE_ROOT')) exit('Direct access is not allowed.');

function url_for($script_path) {
  // add the leading '/' if not present
  if($script_path[0] != '/') {
    $script_path = "/" . $script_path;
  }
  return WWW_ROOT . $script_path;
}

function u($string="") {
  return urlencode($string);
}

function error_404() {
  header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
  exit();
}

function error_500() {
  header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
  exit();
}

function is_post_request() {
  return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function is_get_request() {
  return $_SERVER['REQUEST_METHOD'] == 'GET';
}

function require_login($redirect_url=""){
    global $session;
    if(!$session->isLoggedIn()){
        if($redirect_url !== ""){
            Redirect::to($redirect_url);
        }    
        else{
            Redirect::prevPage();
        }
    }
}


function NY_PAGINATION(){
    global $pagination, $page;

    $html = "<ul class=\"pagination justify-content-center mb-5\">"; 
            $pages = ceil($pagination->offset() - 1);
            if($pagination->total_pages() > 1){
                if($pagination->has_previous_page()){                                 
                    $html .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".escape($_SERVER['PHP_SELF'])."?page=". $pagination->previous_page();
                    $html .= "\"><i class=\"mdi mdi-chevron-left\"></i></a></li>";     
                }
                if($pagination->previous_page() == 0){
                    $html .= "<li class=\"page-item disabled\"><span class=\"page-link\"><i class=\"mdi mdi-chevron-left\"></i></span></li>";           
                }   
                for($i = 1; $i <= $pagination->total_pages(); $i++){
                    if($i == $page){
                        $html .= "<li class=\"page-item active\"><span class=\"page-link\">{$i}</span></li>";
                    }else{
                        $html .= "<li class=\"page-item\"><a href=\"".escape($_SERVER['PHP_SELF'])."?page={$i}\" class=\"page-link\">{$i}</a></li>";
                    }
                }                                       

                if($pagination->total_pages() < $pagination->next_page()){
                    $html .= "<li class=\"page-item disabled\"><span class=\"page-link\"><i class=\"mdi mdi-chevron-right\"></i></span></li>";
                }               
                if($pagination->has_next_page()){                                         
                    $html .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".escape($_SERVER['PHP_SELF'])."?page=". $pagination->next_page();
                    $html .= "\"><i class=\"mdi mdi-chevron-right\"></i></a></li>";
                }                                   
            }                           

    $html .= "</ul>";
    return $html;
}   

function current_user_id(){
    global $session;
    return $session->loggedUserId();
}

function current_user_location(){
    global $session;
    if(isset($session->location)){
        return Location::cityLocation($session->location);
    }
    return "";
}    

function sortby_filters($sortby){
    if (isset($sortby)):
        switch ($sortby) {
            case 'best':
                $sortby = "views DESC";
                break;
            case 'price_asc':
                $sortby = "price ASC";
                break;
            case 'price_desc':
                $sortby = "price DESC";
                break;        
            case 'new':
                $sortby = "added DESC";
                break;
            case 'beds':
                $sortby = "beds DESC";
                break;
        }
        return $sortby;
    else: 
        return "added DESC";   
    endif;
}

function pre($value){
    echo '<pre>';
    print_r($value);
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

function end_post_date($datetime){
    $datetime = new DateTime($datetime);
    $valid_for = new DateInterval(Config::get('end_post_date'));

    $now = new DateTime();

    $expiry_date = clone $datetime;
    $expiry_date->add($valid_for);

    return $now < $expiry_date;
}

// Time functions
function time_ago($time){
    $formated_time = strtotime($time);
    $time_difference = time() - $formated_time;
 
    if($time_difference < 1 ) { return strtoupper('less than 1 second ago'); }
    
    $condition = array( 
            12 * 30 * 24 * 60 * 60  =>  'year',
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
            return strtoupper($t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago');
        }

    }
}

function text_to_datetime($format){
    return strftime($format, time());
}

function mysql_datetime(){
    return text_to_datetime(Config::get('mysql_date_time_format'));
}

function datetime_to_text($datetime="") {
  $unixdatetime = strtotime($datetime);
  return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
} // End Time functions

function page_title($page_title=""){
    if(empty($page_title)  && !isset($page_title)){
        $page_title = "Nyumba Yanga · List your property";
    }
    else{
        $page_title = $page_title;
    }
    return $page_title;
}

function admin_template($template=""){
    global $page_title, $session, $user;
    include(PRIVATE_PATH . '/shared/' . $template);
}

function layout_template($template=""){
    global $page_title, $session, $user;
    include(PRIVATE_PATH . '/shared/' . $template);
}

function output_message($message="",  $type="info") {
    $output = "";   
    if (!empty($message)) {    
        switch ($type) {
            case $type == 'success':
                $output .= "<p class=\"message-success\"><i class=\"mdi mdi-check-circle-outline mdi-18px\"></i><span>".$message."</span></p>";
                break;
            
            case $type == 'warning':
                $output .= "<p class=\"message-warning\"><i class=\"mdi mdi-alert-circle-outline mdi-18px\"></i><span>".$message."</span></p>";
                break;

            case $type == 'info':
                $output .= "<p class=\"message-info\"><i class=\"mdi mdi-information-outline mdi-18px\"></i><span>".$message."</span></p>";
                break;  

            case $type == 'danger':
            $output .= "<p class=\"message-danger\"><i class=\"mdi mdi-alert-circle-outline mdi-18px\"></i><span>".$message."</span></p>";
            break;

            case $type == 'text-danger':
            $output .= "<p class=\"text-danger\">".$message."</span></p>";
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
                $output .= "<p class=\"message-success\"><i class=\"mdi mdi-check-circle-outline mdi-18px\"></i><span>".Session::flash($name)."</span></p>";
                break;
            
            case $type == 'warning':
                $output .= "<p class=\"message-warning\"><i class=\"mdi mdi-alert-circle-outline mdi-18px\"></i><span>".Session::flash($name)."</span></p>";
                break;

            case $type == 'info':
                $output .= "<p class=\"message-info\"><i class=\"mdi mdi-information-outline mdi-18px\"></i><span>".Session::flash($name)."</span></p>";
                break;  

            case $type == 'danger':
            $output .= "<p class=\"message-danger\"><i class=\"mdi mdi-alert-circle-outline mdi-18px\"></i><span>".Session::flash($name)."</span></p>";
            break;   
        }
    }    
    return $output;
}

function log_action($action, $message="") {
    $logfile = SITE_ROOT.'/logs/log.txt';
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
                $output .= "<option value=\"\">Please select --</option>";
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

function fav_add($property_id=0){
    global $session;

    if ($session->isLoggedIn()){
        return '<a href="list_save.php?id='.$property_id.'"style="color:#fff;float:right;padding:0 .4rem;height:2.1rem;margin-top:-.3rem;"><i class="mdi mdi-heart-outline mdi-36px"></i></a>';
    }
}

function fav_remove($property_id=0){
    global $session;

    if ($session->isLoggedIn()){
        return '<a href="list_unsave.php?id='.$property_id.'" style="color:#01e675;float:right;padding:0 .4rem;margin-top:-.3rem;"><i class="mdi mdi-heart mdi-36px"></i></a>';
    }
}

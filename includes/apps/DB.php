<?php

class DB {

    private static $instance = null;
    private        $conn;
    private        $lastquery;
    private        $fetchquery;
    private        $error = false;

    private $fetch_methods = array(
        "FETCH_OBJ"    => PDO::FETCH_OBJ,
        "FETCH_ASSOC"  => PDO::FETCH_ASSOC
    );

    private function __construct(){
        try{
            $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET;
            $options = [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
                PDO::ATTR_PERSISTENT => true,
            ];

            // Open a PDO connection
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
            // Set error reporting up
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Actually use prepared statements
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        }
        catch(PDOException $e) {
            $this->error_handler($e->getMessage());
            die("Error Connecting: " . $e->getMessage());
        }
    }        

    // Use a singleton pattern to connect to the database
    public static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function direct_query($sql=""){
        try{
            $this->lastquery = $this->conn->query($sql);
            return true;
        }
        catch(PDOException $e){
            $this->error_handler($e->getMessage());
            print($e->getMessage()."<br>File: ".$e->getFile()."<br>Line: ".$e->getLine());
            die();
        }
    }

    public function query($sql, $params = array()){
        try{
            $this->lastquery = $this->conn->prepare($sql);
            $x = 1;
            if(count($params)) {
                foreach ($params as $param) {
                    $this->lastquery->bindValue($x, $param);
                    $x++;
                }
            }
            $this->lastquery->execute();
        }
        catch(PDOException $e) {
            $this->error_handler($e->getMessage());
            print($e->getMessage()."<br>File: ".$e->getFile()."<br>Line: ".$e->getLine());
            die();
        }
    }

    public function fetch($result = null, $method = 'FETCH_ASSOC'){
        try{
            if($this->fetchquery == null){
                $this->fetchquery = $this->lastquery;
            }
            if($result == null){
                $result = $this->fetchquery;
            }
            $data = $result->fetch($this->fetch_methods[$method]);
            if($data == false){
                $this->fetchquery = null;
            }
            return $data;
        }    
        catch(PDOException $e) {
            $this->error_handler($e->getMessage());
        }
    }

    public function fetchAll($result = null, $method = 'FETCH_ASSOC'){
        try{
            if($result == null){
                $result = $this->lastquery;
            }
            return $result->fetchAll($this->fetch_methods[$method]);
        }
        catch(PDOException $e){
            $this->error_handler($e->getMessage());
        }
    }

    public function result($column = null, $result = null, $method = 'FETCH_ASSOC'){
        if ($result == null){
            $result = $this->lastquery;
        }        
        $data = $result->fetch($this->fetch_methods[$method]);
        if (empty($column) || $column == null){
            return $data;
        }
        else{
            return $data[$column];
        }
    }

    public function count($result = null){
        try{
            if($result == null){
                $result = $this->lastquery;
            }
            return $result->rowCount();
        }
        catch(PDOException $e){
            $this->error_handler($e->getMessage());
        }
    }

    public function lastInsertId(){
        try{
            return $this->conn->lastInsertId();
        }
        catch(PDOException $e){
            $this->error_handler($e->getMessage());
        }    
    }

    public function transaction(){
        try{
            $this->conn->beginTransaction();
        }
        catch(PDOException $e){
            die($e->getMessage());
        }
    }

    public function rollback(){
        try{
            $this->conn->rollback();
        }
        catch(PDOException $e){
            die($e->getMessage());
        }
    }

    public function commit(){
        try{
            $this->conn->commit();
        }
        catch(PDOException $e){
            die($e->getMessage());
        }
    }

    private function error_handler($error){
        // TODO: DO SOMETHING
        $this->error = debug_backtrace();
        //die(print_r($this->error));
    }

    public function __destruct(){
        // Close the connection at the end
        $this->conn = null;
    }

} 
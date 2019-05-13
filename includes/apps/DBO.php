<?php

/**
 * This store all the common Database CRUD that will be inherited by other classes
 * Using PHP Late Static Binding
 */
Abstract class DBO{

	protected static $table_name;
    protected static $db_fields = array();

 
    public static function findAll(){
        return static::findBySql("SELECT * FROM ".static::$table_name);
    }

    public static function findById($user_id=0){
        $sql = "SELECT * FROM ".static::$table_name." WHERE id = ?";
        $params = array($user_id);
        $result_array = static::findBySql($sql, $params);             
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function findBySql($sql="", $params = array()){
        // The SQL syntax for Selecting or reading database rows is
        // - SELECT fields FROM table
        $result_set = DB::getInstance()->query($sql, $params);
        $object_array = [];
        
        while ($row = DB::getInstance()->fetch($result_set, 'FETCH_ASSOC')) {
            $object_array[] = static::instantiate($row);
        }
        return $object_array;
    }

    //Instantiate the object properties
    protected static function instantiate($record){
        $object  = new static;
        foreach ($record as $attribute => $value) {
            if($object->hasAttribute($attribute)){
                $object->$attribute = $value;
            }
        }
        return $object;
    }

    //Look for matching attributes from the Database table and the class properties
    protected function hasAttribute($attribute){
        $object_vars = $this->attributes();
        return array_key_exists($attribute, $object_vars);
    }

    protected function attributes(){
        // Return an array of attribute keys and their values
        $attributes = array();
        foreach (static::$db_fields as $field) {
            if(property_exists($this, $field)){
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }

    public static function total(){
        $sql = "SELECT COUNT(*) AS total FROM ".static::$table_name;
        DB::getInstance()->direct_query($sql);
        $result = DB::getInstance()->result();
        return array_shift($result);
    }

    public function save(){
       //Updates a record if exits otherwise it will create one
        return isset($this->id) ? $this->update() : $this->create();
    }

    public function create(){
        // The SQL syntax for inserting is
        // - INSERT INTO table_name (key1, key2) VALUES(value1, value2)
        // - Single quotes against all values

        //The table attributs
        $attributes = $this->attributes();

        $sql  = "INSERT INTO ".static::$table_name." (";
        $sql .= join(", ", array_keys($attributes));
        $sql .= ") VALUES (";
        $sql .= join(', ', array_fill(1, count($attributes), '?'));
        $sql .= ")";    
        DB::getInstance()->query($sql, $attributes);
        $this->id = DB::getInstance()->lastInsertId();    
        if(DB::getInstance()->count()){       
            return true;
        }
        return false;
    }

    public function update(){
        // The SQL syntax for updating is
        // - UPDATE table SET key1 = value1, key2 = value2 WHERE condition
        // - Single quotes against all values
        $attributes = $this->attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key} = ?";
        }
        $sql  = "UPDATE ".static::$table_name." SET ";
        $sql .=  join(", ", $attribute_pairs);
        $sql .=  " WHERE id=".$this->id;
        DB::getInstance()->query($sql, $attributes);
        return (DB::getInstance()->count() === 1) ?  true : false;
    }

    public function delete(){
        // The SQL syntax is for updating is
        // - DELETE FROM table WHERE id = value LIMIT 1
        $sql  = "DELETE FROM ".static::$table_name;
        $sql .= " WHERE id=".$this->id;
        $sql .= " LIMIT 1";
        DB::getInstance()->query($sql);
        return (DB::getInstance()->count() === 1) ?  true : false;
    }
}
<?php

/**
 * The Remember me class
 */
class Remember extends DBO{	
	protected static $table_name = "remember";
    protected static $db_fields = ['user_id', 'hashkey'];

    public $user_id;
    public $hashkey;
}

/**
 * The location class
 */
class Saved extends DBO{	
	protected static $table_name = "saved";
    protected static $db_fields = ['id', 'user_id', 'property_id', 'saved'];

    public $id;
    public $user_id;
    public $property_id;
    public $saved;

}

/**
 * The location class
 */
class Location extends DBO{	
	protected static $table_name = "location";
    protected static $db_fields = ['id', 'city_id', 'location', 'description'];

    public $id;
    public $city_id;
    public $location;
    public $description;

	public static function AllLocations(){
		$locations = array();
		DB::getInstance()->direct_query("SELECT id, location FROM ".self::$table_name." ORDER BY location");
		while ($row = DB::getInstance()->fetch()) {
			$locations[ucwords($row['location'])] = $row['id'];
		}
		return $locations;
	}

	public static function findLocationOn($location_id){
		$sql = "SELECT location FROM ".self::$table_name." WHERE id=?";
		DB::getInstance()->query($sql, array($location_id));
		$result = DB::getInstance()->result();
		return !empty($result) ? ucwords(array_shift($result)) : "";
	}

	public static function cityLocation($location_id, $breadcrumb=false){
		DB::getInstance()->query("SELECT location, city_id FROM ".self::$table_name." WHERE id=?", array($location_id));
		while($location_result = DB::getInstance()->fetch()){
			$location = $location_result['location'];
			$city_id = $location_result['city_id'];
		}
		DB::getInstance()->query("SELECT city FROM city WHERE id=?", array($city_id));
		$city_result = DB::getInstance()->result();
		$city =  !empty($city_result) ? ucwords(array_shift($city_result)) : "";

		if($breadcrumb == true && !empty($location) && !empty($city)){
			$url  = '<a href="index.php">Home</a>&nbsp;<i class="mdi mdi-chevron-right"></i>&nbsp;';
			$url .= '<a href="city.php?q='.rawurlencode(City::findCityOn($city_id)).'">'. ucwords($city) .'</a>&nbsp;<i class="mdi mdi-chevron-right"></i>&nbsp;';
			$url .= ucwords($location);
			return $url;
		}
		elseif(!empty($location) && !empty($city)){
		 	return ucwords($location) .", ". ucwords($city); 
		}
		
		return "";
	}

}

/**
 * The city class
 */
class City extends DBO{	
	protected static $table_name = "city";
    protected static $db_fields = ['id', 'city', 'description'];

    public $id;
    public $city;    
    public $description;

	public static function AllCities(){
		$cities = array();
		DB::getInstance()->direct_query("SELECT id, city FROM ".self::$table_name." ORDER BY city");
		while ($row = DB::getInstance()->fetch()) {
			$cities[ucwords($row['city'])] = $row['id'];
		}
		return $cities;
	}

	public static function findCityOn($city_id){
		$sql = "SELECT city FROM ".self::$table_name." WHERE id=?";
		DB::getInstance()->query($sql, array($city_id));
		$result = DB::getInstance()->result();
		return !empty($result) ? ucwords(array_shift($result)) : "";
	}

}
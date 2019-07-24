<?php

/**
 * The Remember me class
 */
class Remember extends DBO{	
	protected static $table_name = "remember";
    protected static $columns = ['user_id', 'hashkey'];

    public $user_id;
    public $hashkey;
}

/**
 * The Saved property class
 */
class Saved extends DBO{	
	protected static $table_name = "saved";
    protected static $columns = ['id', 'user_id', 'property_id', 'saved'];

    public $id;
    public $user_id;
    public $property_id;
    public $saved;

}

/**
 * The Amenities class
 */
class Amenities extends DBO{	
	protected static $table_name = "amenities";
    protected static $columns = ['property_id', 'feature_id'];

    public $feature_id;
    public $property_id;

    public function add(array $features_array = [], int $property_id){
    	if (!empty($features_array)) {
		    foreach ($features_array as $key => $value) {
        		$this->property_id = $property_id;
        		$this->feature_id  = $value;
        		$this->create();
        	}
        	return true;	
        }else{
        	return false;
        }	
    }

}

/**
 * The Location class
 */
class Location extends DBO{	
	protected static $table_name = "location";
    protected static $columns = ['id', 'city_id', 'location', 'description'];

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
 * The City class
 */
class City extends DBO{	
	protected static $table_name = "city";
    protected static $columns = ['id', 'city', 'description'];

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
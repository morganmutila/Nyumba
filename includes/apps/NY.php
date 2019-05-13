<?php

/**
 * The location class
 */
class SavedProperty extends DBO{	
	protected static $table_name = "saved_property";
    protected static $db_fields = array('id', 'user_id', 'property_id', 'created');

    public $id;
    public $user_id;
    public $property_id;
    public $created;

}



/**
 * The location class
 */
class Location extends DBO{	
	protected static $table_name = "location";
    protected static $db_fields = array('id', 'city_id', 'location', 'description');

    public $id;
    public $city_id;
    public $location;
    public $description;

	public function AllLocations(){
		$locations = array();
		DB::getInstance()->direct_query("SELECT id, location FROM location ORDER BY location");
		while ($row = DB::getInstance()->fetch()) {
			$locations[$row['location']] = $row['id'];
		}
		return $locations;
	}

	public static function findLocationOn($location_id){
		$sql = "SELECT location FROM location WHERE id=?";
		$params = array($location_id);
		DB::getInstance()->query($sql, $params);
		$result = DB::getInstance()->result();
		return !empty($result) ? array_shift($result) : "";
	}
}
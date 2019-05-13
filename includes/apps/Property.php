<?php

class Property extends DBO{

	protected static $table_name = "property";
    protected static $db_fields = array('id', 'user_id', 'location_id', 'type', 'address',
    'beds', 'baths', 'terms', 'cphoto', 'contact_number', 'contact_email','reference', 'listed_by', 'size', 'status', 'price', 'available', 'units','views', 'market', 'owner', 'description', 'added', 'flags');


    public $id;
    public $user_id;
    public $location_id;
    public $type;
    public $address;
    public $beds;
    public $baths;
    public $terms;
    public $cphoto;
    public $contact_number;
    public $contact_email;
    public $reference;
    public $listed_by;
    public $size;
    public $status;
    public $price;
    public $units;
    public $available;
    public $views;
    public $market;
    public $owner;
    public $description;
    public $added;
    public $flags;


    public static function propertyFeatures($type="indoor"){
        //These will come from the database
        $property_features = array();
        if($type == "indoor"){
            $sql = "SELECT id, feature FROM property_feature WHERE type = 'indoor'  ORDER BY id";
        }elseif($type == "outdoor"){
            $sql = "SELECT id, feature FROM property_feature WHERE type = 'outdoor' ORDER BY id";
        }    
        DB::getInstance()->direct_query($sql);
        while($row = DB::getInstance()->fetch()){
            $property_features[$row['feature']] = $row['id'];
        }

        return $property_features;
    }   

    public function propertyStatus(){
        switch ($this->status) {                           
            case 1:
                $status = "added";
                break;
            case 2:
                $status = "review";
                break;
            case 3:
                $status = "pending";
                break;      
            case 4:
                $status = "suspended";
                break;                
            case 5:
                $status = "listed";
                break;       
            case 6:
                $status = "archieved";
                break;
        }
         return strtoupper($status);
    }    

    public function rentTerms(){
        if($this->market == "rent"){
            switch ($this->terms) { 
                case 1:
                    $terms = "/ mon";
                    break;
                case 2:
                    $terms = "/ 2 mon";
                    break;      
                case 3:
                    $terms = "/ 3 mon";
                    break;                
                case 6:
                    $terms = "/ 6 mon";
                    break;       
                case 12:
                    $terms = "/ yr";
                    break;
            }
            return strtolower($terms);
        }
        return "";
    }    

    public function location(){ 
        $location = Location::findLocationOn($this->location_id);
        return $location;
    }

    public function propertyUser(){ 
        $user = User::findbyId($this->user_id);
        return !empty($user) ? $user->fullname() : false;
    }

    public static function image(){
        // return PPhoto::imagePath();
        return  "________________
                |                |
                |                |
                |                |
                |                |
                |                |
                |                |
                |________________|";
    }

    public function __toString(){
        $output  = "ID: ". 		    	$this->id ."<br>";
        $output .= "Address: ". 	    $this->address ."<br>";
		$output .= "Bedrooms: ".    	$this->beds."<br>";
		$output .= "Bathrooms: ".   	$this->baths."<br>";
		$output .= "Plot Size: ".   	$this->size." sqft"."<br>";
		$output .= "Added On: ".		$this->added."<br>";
		$output .= "Description: ". 	$this->description."<br>";
		$output .= "Owner: ". 			$this->owner."<br>";
		$output .= "Rent: K". 			$this->price."<br>";
		$output .= "Available: ". 		$this->available."<br>";
		$output .= "Views: ".			$this->views."<br>";
		$output .= "Market Name: ". 	ucfirst($this->market)."<br>";
        return $output;
    }
}



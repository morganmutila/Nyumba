<?php

class Property extends DBO{

	protected static $table_name = "property";
    protected static $db_fields = ['id', 'user_id', 'location_id', 'type', 'address',
    'beds', 'baths', 'terms', 'photo', 'contact_number', 'contact_email','reference', 'listed_by', 'size', 'status', 'price', 'price_old', 'negotiable', 'available', 'units','views', 'market', 'contact_name', 'description', 'added', 'flags'];

    private const PROPERTY_TYPE = [
        1   => "House",
        2   => "Flat",
        3   => "Apartment",
        4   => "Semi-detached",
        5   => "Townhouse",
        6   => "Condo"
    ];

    public $id;
    public $user_id;
    public $location_id;
    public $type;
    public $address;
    public $beds;
    public $baths;
    public $terms;
    public $photo;
    public $contact_number;
    public $contact_email;
    public $reference;
    public $listed_by;
    public $size;
    public $status;
    public $price;
    public $price_old;
    public $negotiable;
    public $units;
    public $available;
    public $views;
    public $market;
    public $contact_name;
    public $description;
    public $added;
    public $flags;


    public static function features($type="indoor"){
        //These will come from the database
        $property_features = array();
        if($type == "indoor"){
            $sql = "SELECT id, feature FROM features WHERE type = 'indoor'  ORDER BY id";
        }elseif($type == "outdoor"){
            $sql = "SELECT id, feature FROM features WHERE type = 'outdoor' ORDER BY id";
        }    
        DB::getInstance()->direct_query($sql);
        while($row = DB::getInstance()->fetch()){
            $property_features[$row['feature']] = $row['id'];
        }

        return $property_features;
    }   

    public function photo(){
        return Photo::imagePath($this->id);
    }

    public function status(){
        switch ($this->status) {                           
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
                $status = "added";
                break;
            case 7:
                $status = "pending";
                break;      
            case 8:
                $status = "suspended";
                break;                
            case 9:
                $status = "archieved";
                break;       
            case 10:
                $status = "listed";
                break;
        }
        return strtoupper($status);
    }    

    public function type(){
        if($this->type > 0){
            return self::PROPERTY_TYPE[$this->type];
        } else {
            return "";
        }
    }

    public function priceCut(){
        if((int) $this->price_old == true){
            if($this->price > $this->price_old){
                $price_diff = $this->price - $this->price_old;
                $price_diff = "<span style=\"color:#1db954;font-size:.85rem;\"><i class=\"mdi mdi-arrow-up\"></i>&nbsp;".amount_format($price_diff)."</span>";
            }elseif($this->price < $this->price_old){
                $price_diff = $this->price_old - $this->price;
                $price_diff = "<span style=\"color:red;font-size:.85rem;\"><i class=\"mdi mdi-arrow-down\"></i>&nbsp;".amount_format($price_diff)."</span>";
            }
            else{
                $price_diff = "";
            }
        }    
        else{
            $price_diff = "";
        }
        return $price_diff;
    }

    public function terms(){        
        if($this->market == "rent"){
            $terms = "";
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
    }    

    public function location(){ 
        $location = Location::findLocationOn($this->location_id);
        return ucwords($location);
    }

    public function manager(){ 
        $user = User::findbyId($this->user_id);
        return !empty($user) ? $user->fullname() : "Unknown";
    }


}



<?php

class Property extends DBO{

	protected static $table_name = 'property';
    protected static $columns = ['id', 'user_id', 'location_id', 'type', 'address',
    'beds', 'baths', 'terms', 'photo', 'contact_number', 'contact_email','reference', 'listed_by', 'size', 'status', 'price', 'negotiable', 'available', 'units','views', 'market', 'contact_name', 'description', 'added', 'flags'];

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
    public $negotiable;
    public $units;
    public $available;
    public $views;
    public $market;
    public $contact_name;
    public $description;
    public $added;
    public $flags;

    private const PROPERTY_TYPE = [
        1   =>  "House",
        2   =>  "Flat",
        3   =>  "Apartment",
        4   =>  "Semi-detached",
        5   =>  "Townhouse",
        6   =>  "Condo"
    ];

    private const RENT_TERMS = [
        1   =>  "/ mon",
        2   =>  "/ 2 mon",
        3   =>  "/ 3 mon",             
        6   =>  "/ 6 mon",   
        12  =>  "/ yr"
    ];

    public function __construct($args=[]) {
        $this->user_id          = $args['user_id'] ?? '';
        $this->location_id      = $args['location_id'] ?? '';
        $this->address          = $args['address'] ?? '';
        $this->beds             = $args['beds'] ?? '';
        $this->baths            = $args['baths'] ?? '';
        $this->terms            = $args['terms'] ?? '';
        $this->size             = $args['size'] ?? 0;
        $this->type             = $args['type'] ?? '';
        $this->price            = $args['price'] ?? 0;
        $this->price_old        = $args['price_old'] ?? 0;
        $this->negotiabe        = $args['negotiabe'] ?? 0;
        $this->description      = $args['description'] ?? '';
        $this->photo            = $args['photo'] ?? '';
        $this->contact_number   = $args['contact_number'] ?? '';
        $this->contact_email    = $args['contact_email'] ?? '';
        $this->contact_name     = $args['contact_name'] ?? '';
        $this->available        = $args['available'] ?? '';
        $this->reference        = $args['reference'] ?? '';
        $this->status           = $args['status'] ?? '';
        $this->units            = $args['units'] ?? 1;
        $this->views            = $args['views'] ?? 0;
        $this->flags            = $args['flags'] ?? 0;
        $this->available        = $args['available'] ?? '';
        $this->listed_by        = $args['listed_by'] ?? '';
        $this->market           = $args['market'] ?? '';
        $this->added            = $args['added'] ?? '0000-00-00';
        $this->status           = $args['status'] ?? 0;
    }

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

    public function details(){
        return "{$this->type} {$this->address} {$this->Location()}";
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

    public function priceValue(){
        if(isset($this->price) && !empty($this->price)){            
            if($this->negotiable == true){
                return "<div class=\"font-weight-bold\" style=\"font-size:1.15rem;\">".amount_format($this->price) . "<small class=\"text-success ml-2\" style=\"font-size:.8rem;\">NEG</small></div> <div class=\"text-info pr-2 small\">" . $this->type." for ".$this->market."</div>";
            }else{
                return "<div class=\"font-weight-bold\" style=\"font-size:1.15rem;\">".amount_format($this->price) . "<small>&nbsp;" . $this->terms() . "</small></div> <div class=\"text-info pr-2 small\">" . $this->type." for ".$this->market."</div>";
            }    
        }    
        return "Call for price";
    }

    public function type(){
        if($this->type > 0){
            return self::PROPERTY_TYPE[$this->type];
        } else {
            return "Unknown";
        }
    }

    public function terms(){        
        if($this->market == "rent"){
            return strtolower(self::RENT_TERMS[$this->terms]);
        }else{
            return '';
        }
    }    

    public function plotSize(){
        if(isset($this->size) && !empty($this->size)){
            return number_format($this->size). " Sqft";
        }
        return "Size unavailable";
    }

    public function location(){ 
        $location = Location::cityLocation($this->location_id);
        return ucwords($location);
    }

    public function manager(){ 
        $user = User::findbyId($this->user_id);
        return !empty($user) ? $user->fullname() : "Unknown";
    }

}



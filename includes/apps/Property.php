<?php

class Property extends DBO{

	protected static $table_name = "property";
    protected static $db_fields = ['id', 'user_id', 'location_id', 'type', 'address',
    'beds', 'baths', 'terms', 'cphoto', 'contact_number', 'contact_email','reference', 'listed_by', 'size', 'status', 'price', 'price_old', 'negotiable', 'available', 'units','views', 'market', 'owner', 'description', 'added', 'flags'];


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
    public $price_old;
    public $negotiable;
    public $units;
    public $available;
    public $views;
    public $market;
    public $owner;
    public $description;
    public $added;
    public $flags;


    public static function features($type="indoor"){
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

    public function cphoto(){
        return PPhoto::imagePath($this->id);
    }

    public function status(){
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

    public function priceCut(){
        if((int) $this->price_old == true /*&& $this->market == "sale"*/){
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
        return !empty($user) ? $user->fullname() : false;
    }


}



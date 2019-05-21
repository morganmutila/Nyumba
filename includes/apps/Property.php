<?php

class Property extends DBO{

	protected static $table_name = "property";
    protected static $db_fields = array('id', 'user_id', 'location_id', 'type', 'address',
    'beds', 'baths', 'terms', 'cphoto', 'contact_number', 'contact_email','reference', 'listed_by', 'size', 'status', 'price','negotiable', 'available', 'units','views', 'market', 'owner', 'description', 'added', 'flags');


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
    public $negotiable;
    public $units;
    public $available;
    public $views;
    public $market;
    public $owner;
    public $description;
    public $added;
    public $flags;

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private $temp_path;
    public $upload_dir;
    public $errors=array();
      
    private $upload_errors = array(
        UPLOAD_ERR_OK         => "No errors.",
        UPLOAD_ERR_INI_SIZE   => "Larger than upload_max_filesize.",
        UPLOAD_ERR_FORM_SIZE  => "Larger than form MAX_FILE_SIZE.",
        UPLOAD_ERR_PARTIAL    => "Partial upload.",
        UPLOAD_ERR_NO_FILE    => "No file.",
        UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
        UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
        UPLOAD_ERR_EXTENSION  => "File upload stopped by extension."
    );

    // public function __construct(){     
    //     // Make sure the upload directory exits and we have file perms.
    //     $this->createUploadFolder();
    // }

    // Pass in $_FILE(['uploaded_file']) as an argument
    // public function attachFile($file) {
    //     // Perform error checking on the form parameters
    //     if(!$file || empty($file) || !is_array($file)) {
    //       // error: nothing uploaded or wrong argument usage
    //       $this->errors[] = "No file was uploaded.";
    //       return false;
    //     } elseif($file['error'] != 0) {
    //       // error: report what PHP says went wrong
    //       $this->errors[] = $this->upload_errors[$file['error']];
    //       return false;
    //     } else {
    //         // Set object attributes to the form parameters.
    //       $this->temp_path  = $file['tmp_name'];
    //       $this->filename   = basename($file['name']);
    //         // Don't worry about saving anything to the database yet.
    //         return true;
    //     }
    // }
  
    // public function save() {
    //     // A new record won't have an id yet.
    //     if(isset($this->id)) {
    //         // Really just to update the caption
    //         $this->update();
    //     } else {
    //         // Make sure there are no errors
            
    //         // Can't save if there are pre-existing errors
    //         if(!empty($this->errors)) { return false; }
          
    //         // Make sure the caption is not too long for the DB
    //         // if(strlen($this->caption) > 255) {
    //         //     $this->errors[] = "The caption can only be 255 characters long.";
    //         //     return false;
    //         // }
        
    //         // Can't save without filename and temp location
    //         if(empty($this->filename) || empty($this->temp_path)) {
    //             $this->errors[] = "The file location was not available.";
    //             return false;
    //         }
            
    //         // Determine the target_path
    //         $target_path = SITE_ROOT .DS. 'public' .DS. $this->upload_dir .DS. $this->filename;
          
    //         // Make sure a file doesn't already exist in the target location
    //         if(file_exists($target_path)) {
    //             $this->errors[] = "The file {$this->filename} already exists.";
    //             return false;
    //         }
            
    //         // Attempt to move the file 
    //         if(move_uploaded_file($this->temp_path, $target_path)) {
    //         // Success
    //             // Save a corresponding entry to the database
    //             if($this->create()) {
    //                 // We are done with temp_path, the file isn't there anymore
    //                 unset($this->temp_path);
    //                 return true;
    //             }
    //         } else {
    //             // File was not moved.
    //         $this->errors[] = "The file upload failed, possibly due to incorrect permissions on the upload folder.";
    //         return false;
    //         }
    //     }
    // }
    
    // public function destroy() {
    //     // First remove the database entry
    //     if($this->delete()) {
    //         // then remove the file
    //       // Note that even though the database entry is gone, this object 
    //         // is still around (which lets us use $this->image_path()).
    //         $target_path = SITE_ROOT.DS.'public'.DS.$this->image_path();
    //         return unlink($target_path) ? true : false;
    //     } else {
    //         // database delete failed
    //         return false;
    //     }
    // }

    // private function createUploadFolder(){      
    //     if(!file_exists(UPLOAD_FOLDER)) {
    //         mkdir(UPLOAD_FOLDER, 0777, true);
    //         $this->upload_dir = UPLOAD_FOLDER;
    //         return true;
    //     }
    //     else{
    //         $this->upload_dir = UPLOAD_FOLDER;
    //         return true;
    //     }
    //     return false;
    // }

    // public function imagePath() {
    //     if(!$this->cphoto || !file_exists($this->cphoto)){
    //         return $this->upload_dir.DS.'default.png';
    //     }
    //     else{
    //         return $this->upload_dir.DS.$this->cphoto;
    //     }
    // }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
        return $location;
    }

    public function propertyUser(){ 
        $user = User::findbyId($this->user_id);
        return !empty($user) ? $user->fullname() : false;
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



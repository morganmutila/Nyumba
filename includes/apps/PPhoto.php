<?php

class PPhoto extends DBO{

	protected static $table_name = "property_photo";
    protected static $db_fields  = ['id', 'property_id', 'filename', 'type','size','width','height', 'last_update'];

    public $id;
    public $property_id;
    public $filename;
    public $type;
    public $size;
    public $width;
    public $height;
    public $last_update;

    private $upload_errors = array();
    private static $upload_dir = UPLOAD_FOLDER;

    public function attachFile($files, $property_id=0){
        
        // ---------- MULTIPLE UPLOADS ----------

        // as it is multiple uploads, we will parse the $_FILES array to reorganize it into $files
        $file_array = array();
        foreach ($files as $k => $l) {
            foreach ($l as $i => $v) {
                if (!array_key_exists($i, $file_array)){
                    $file_array[$i] = array();
                }    
                $file_array[$i][$k] = $v;
            }
        }
        // now we can loop through $files, and feed each element to the class
        foreach ($file_array as $file) {

            $handle = new upload($file);
            // Set variables
            $handle->allowed = ['image/*'];
            $handle->file_safe_name = true;
            $handle->file_auto_rename = false;

            $handle->image_ratio_crop = true;
            $handle->image_resize     = true;
            $handle->image_ratio_y    = true;
            $handle->image_x          = 450;
            $handle->image_contrast   = 20;


            if ($handle->uploaded) {
                //Yes, the file is on the server           
                $handle->process(self::$upload_dir);

                // we check if everything went OK
                if ($handle->processed) {
                    // everything was fine  
                    $this->property_id = $property_id;
                    $this->filename    = $handle->file_dst_name;
                    $this->type        = $handle->file_dst_name_ext;
                    $this->size        = filesize($handle->file_dst_pathname);
                    $this->width       = $handle->image_dst_x;
                    $this->height      = $handle->image_dst_y;
                    $this->create();
                    //Increment the ID to avoid duplicate IDs
                    $this->id++;
                }
                else {
                    // one error occured
                    $this->upload_errors[] = $handle->error;
                }  
            }
            else {
               $this->upload_errors[] = $handle->error;
            }
            // we delete the Original files
            $handle->clean();  
        } 
    }   

    public function uploadSuccess(){
        return empty($this->upload_errors) ? true : false;
    }   

    public function uploadErrors(){
        return $this->upload_errors;
    }    

    public static function imagePath(int $property_id=0) {
        $sql  = "SELECT filename FROM ". self::$table_name;
        $sql .= " WHERE property_id = ?";
        $sql .= " ORDER BY RAND()";
        $sql .= " LIMIT 1";
        DB::getInstance()->query($sql, array($property_id));
        $result = DB::getInstance()->result();
        $path = !empty($result) ? array_shift($result) : "";
        if($path == ""){
            return UPLOAD_FOLDER.DS.'default.png';
        }
        else{
            return UPLOAD_FOLDER.DS.$path;
        }
    }
    
    public function destroy() {
        // First remove the database entry
        if($this->delete()) {
            // then remove the file
          // Note that even though the database entry is gone, this object 
            // is still around (which lets us use $this->image_path()).
            $target_path = $this->upload_dir.DS.$this->image_path();
            return unlink($target_path) ? true : false;
        } else {
            // database delete failed
            return false;
        }
    }
}



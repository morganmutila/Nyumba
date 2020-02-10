<?php

class Photo extends DBO{

	protected static $table_name = "photos";
    protected static $columns  = ['id', 'property_id', 'filename', 'type','size','width','height'];

    public $id;
    public $property_id;
    public $filename;
    public $type;
    public $size;
    public $width;
    public $height;

    private $upload_errors = array();
    private static $upload_dir = UPLOAD_FOLDER;

    public function attachFile($files=null, $property_id=0, $multiple=false){
        if($multiple == true) {
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
                $handle->allowed            = ['image/*'];
                $handle->file_safe_name     = true;
                $handle->file_auto_rename   = true;
                $handle->file_new_name_body = $this->rename();

                $handle->image_ratio_crop   = true;
                $handle->image_resize       = true;
                $handle->image_ratio_y      = true;
                $handle->image_x            = 300;
                $handle->image_y            = 100;
                $handle->image_contrast     = 20;

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
        else{
            // ---------- SINGLE UPLOADS ----------
            $handle = new upload($files);
            // Set variables
            $handle->allowed            = ['image/*'];
            $handle->file_safe_name     = true;
            $handle->file_auto_rename   = false;
            $handle->file_new_name_body = $this->rename();

            $handle->image_ratio_crop   = true;
            $handle->image_resize       = true;
            $handle->image_ratio_y      = true;
            $handle->image_x            = 640;
            $handle->image_y            = 480;
            $handle->image_contrast     = 20;



            if ($handle->uploaded) {
                //Yes, the file is on the server           
                $handle->process(self::$upload_dir);

                // we check if everything went OK
                if ($handle->processed) {
                    // everything was fine  
                    $this->property_id     = $property_id;
                    $this->filename    = $handle->file_dst_name;
                    $this->type            = $handle->file_dst_name_ext;
                    $this->size            = filesize($handle->file_dst_pathname);
                    $this->width           = $handle->image_dst_x;
                    $this->height          = $handle->image_dst_y;
                    $this->create();
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

    public function generateHash($length = 32) {

        # don't add vowels and we won't get dirty words...
        $chars = 'BCDFGHJKLMNPQRSTVWXYZbcdfghjklmnpqrstvwxyz1234567890';

        # length of character list
        $chars_length = (strlen($chars) - 1);

        # crete our string
        $string = $chars{
        rand(0, $chars_length)};

        # generate random string
        for ($i = 1; $i < $length; $i = strlen($string)) {

            # grap a random character
            $r = $chars{
            rand(0, $chars_length)};

            # make sure the same characters don't appear next to each other
            if ($r != $string{
            $i - 1}) $string .= $r;
        }

        return $string;
    }

    public function rename(){
        return "NY".$this->generateHash(8).time();
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
            return self::$upload_dir.DS.'default.png';
        }
        else{
            return self::$upload_dir.DS.$path;
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



<?php
/**
 * Class File Upload
 * @author Heinz K, Tech College 2016
 */
class FileUpload {

    public $root;
    public $destination;
    public $max_file_size;
    public $valid_mimes;
    private $fullpath;
    private $finfo;    
    private $default_permissions;
    
    const MSG_OK = 0;
    const MSG_NOFILE = 1;
    const MSG_INVALIDMIMETYPE = 2;
    const MSG_FILETOOBIG = 3;
    const MSG_FILENOTUPLOADED = 4;
    
    /**
     * Class Constructor
     * Initializes properties and sets file info
     */
    public function __construct() {
        $this->root = filter_input(INPUT_SERVER, "DOCUMENT_ROOT");
        $this->destination = "/images/";
        $this->max_file_size = 10485760;
        $this->valid_mimes = "jpg,jpeg,gif,png";
    }

    /**
     * Method Upload
     * Sets destination path and checks mime type and filesize
     * If true run method move_file
     * @return int / string
     */
    function upload() {
        $this->set_destination();
        foreach($_FILES["files"]["error"] as $key => $value) {
            if($value === 0) {
                if($this->check_mime($_FILES["files"]["tmp_name"][$key]))  {
                    if($this->check_size($_FILES["files"]["size"][$key])) {
                        $this->move_file($_FILES["files"]["tmp_name"][$key], $_FILES["files"]["name"][$key]);
                    } else {
                        echo self::MSG_FILETOOBIG;
                    }
                } else {
                    echo self::MSG_INVALIDMIMETYPE;
                }
            } else {
                echo $value;
            }
        }
    }

    /**
     * Method Set Destination
     * Sets the full path for the destination dir
     */
    private function set_destination() {
        $this->fullpath = $this->root . $this->destination;
    }

    /**
     * Method Check Mime
     * Checks if file mime type is listed in the valid mimes
     * @return bool Returns true/false
     */
    private function check_mime($value) {
        $arrValidMimes = explode(",", $this->valid_mimes);
        $this->finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = substr($this->finfo->file($value),strrpos($this->finfo->file($value),"/")+1);        
        return (in_array($mime,$arrValidMimes)) ? TRUE : FALSE;
    }

    /**
     * Method Check Size
     * Checks if file size is below the max size limit
     * @return bool Returns true/false
     */
    private function check_size($value) {
        return ($value > $this->max_file_size) ? FALSE : TRUE;
    }

    /**
     * Method Move File
     * Moves the file to the given destination directory
     * @return int/string Returns the new path of the file
     */
    private function move_file($value, $filename) {
        $name = substr($filename, 0,strrpos($filename, "."));
        $newfilename = getWebsafe($name) . "." . substr($filename, strrpos($filename, ".")+1, strlen($filename));
        if(!move_uploaded_file($value, $this->fullpath . $newfilename)) {
            return self::MSG_FILENOTUPLOADED;
        } else {
            return $this->destination . $filename;
        }
    }

    /**
     * Checks whether destination folder exists
     *
     * @return bool
    protected function destination_exist() {
        return is_writable($this->root . $this->destination);
    }

    /**
     * Create path to destination
     *
     * @param string $dir
     * @return bool
    protected function create_destination() {
        return mkdir($this->root . $this->destination, $this->default_permissions, true);
    }

    /**
     * Set unique filename
     *
     * @return string
    protected function create_new_filename() {
        $filename = sha1(mt_rand(1, 9999) . $this->destination . uniqid()) . time();
        $this->set_filename($filename);
    }
    */
}
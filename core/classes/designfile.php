<?php
class designfile extends design {
    protected $dbTable = "designfile";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();

    public function __construct() {
        parent::__construct($this->dbTable);
        $this->arrColumns["vcFileType"]["Formtype"] = parent::INPUT_SELECT;
        $this->arrColumns["txContent"]["Formtype"] = parent::INPUT_CODEEDITOR;
    }
        
    /**
     * List topics by topic group name
     * @param type $iTopicGroupID
     * @return type
     */
    public function listbyparent($iDesignID) { 
        $this->arrLabels = array(
            "opts" => "Options",
            "vcFileName" => $this->arrColumns["vcFileName"]["Label"],
            "vcFileType" => $this->arrColumns["vcFileType"]["Label"],
            "daCreated" => $this->arrColumns["daCreated"]["Label"],
        );         
        
        $params = array($iDesignID);
        $strSelect = "SELECT * " . 
                        "FROM designfile " . 
                        "WHERE iDesignID = ? " . 
                        "AND iDeleted = 0";
        return $this->db->_fetch_array($strSelect,$params);
    } 
    
    /**
     * Select file by id
     * @param int $iItemID
     * @return array
     */
    public function getItem($iItemID) {
        $this->arrValues = crud::getItem($iItemID);
        foreach ($this->arrValues as $key => $value) {
            $this->$key = $value;
        }
    }
    
    /**
     * Select filecontent by id
     * @param int $iItemID
     * @return array
     */
    public function getItemContent($iItemID) {
        $params = array($iItemID);
        $strSelect = "SELECT txContent FROM designfile WHERE iFileID = ?";
        $this->arrValues = $this->db->_fetch_value($strSelect,$params);
    }    
    
    /**
     * 
     */
    public function save() {
        $iAction = filter_input(INPUT_POST, "iFileID", FILTER_SANITIZE_STRING);
        
        if($iAction < 0) {
            $vcFileName = filter_input(INPUT_POST, "vcFileName", FILTER_SANITIZE_STRING);
            $vcFileType = filter_input(INPUT_POST, "vcFileType", FILTER_SANITIZE_STRING);
            $iDesignID = filter_input(INPUT_POST, "iDesignID", FILTER_SANITIZE_NUMBER_INT);
            
            $this->arrColumns["iDesignID"]["Value"] = $iDesignID;
            $this->arrColumns["vcFileType"]["Value"] = $vcFileType;
            $this->arrColumns["vcFileName"]["Value"] = getWebsafe($vcFileName) . "." . 
                                $this->arrTmpls[$this->arrColumns["vcFileType"]["Value"]][0];
            $this->arrColumns["txContent"]["Value"] = $this->arrTmpls[$vcFileType][1];
            
        }        
        
        $iFileID = crud::saveItem();
        $this->updatefile();
        return $iFileID;
    }
    
    /**
     * Method Delete
     */
    public function delete($iItemID) {
        parent::delete($iItemID);
    } 
        
    public function updatefile() {
        $filepath = $this->filepath . "/app" . $this->arrColumns["iDesignID"]["Value"] . "/";
        $filename = $this->arrColumns["vcFileName"]["Value"];
        $file = $filepath . $filename;
        $handle = fopen($file, "w");
        fwrite($handle,$this->arrColumns["txContent"]["Value"]);
        fclose($handle);    
    }    
        
    public function deletefile($iFileID) {
        $this->getItem($iFileID);
        $this->filepath .= "app" . $this->iDesignID . "/";
        unlink($this->filepath . $this->vcFileName);
        $params = array($this->iFileID);
        $strUpdate = "UPDATE designfile SET iDeleted = 1 WHERE iFileID = ?";
        $this->db->_query($strUpdate,$params);
    }    
}

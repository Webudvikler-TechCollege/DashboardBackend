<?php
class event extends crud {
    protected $dbTable = "event";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();
    public $useTopics = 1;
    public $module = crud::MOD_EVENT;

    public function __construct() {
        parent::__construct($this->dbTable, $this->useTopics, $this->module);
        $this->arrColumns["daStart"]["Formtype"] = parent::INPUT_DATETIME;
        $this->arrColumns["daStop"]["Formtype"] = parent::INPUT_DATETIME;
        $this->arrColumns["vcImage"]["Formtype"] = parent::INPUT_IMAGEEDITOR;
        
    }
        
    /**
     * Select matching rows from a specific query
     * @param string $strSelect
     * @return array Returns an array with the given data
     */
    public function getall() {     
        $strSelect = "SELECT * " . 
                        "FROM " . $this->dbTable . " " .  
                        "WHERE iDeleted = 0";
        return $this->db->_fetch_array($strSelect);
    } 
    
    /**
     * Select song by id
     * @param int $iItemID
     * @return array
     */
    public function getItem($iItemID) {
        $this->arrValues = parent::getItem($iItemID);
        foreach ($this->arrValues as $key => $value) {
            $this->$key = $value;
        }
    }
    
    /**
     * 
     */
    public function save() {
        return parent::saveItem();
    }
    
    /**
     * Method Delete
     */
    public function delete($iItemID) {
        parent::delete($iItemID);
    } 
    
}

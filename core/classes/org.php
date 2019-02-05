<?php
class org extends crud {
    protected $dbTable = "org";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();
    public $useTopics = 1;
    public $module = crud::MOD_ORG;
    

    public function __construct() {
        parent::__construct($this->dbTable, $this->useTopics, $this->module);
    }
        
    /**
     * Select matching rows from a specific query
     * @param string $strSelect
     * @return array Returns an array with the given data
     */
    public function getall() {     
        $strSelect = "SELECT * " . 
                        "FROM " . $this->dbTable . " " .
                        "WHERE iDeleted = 0";;
        return $this->db->_fetch_array($strSelect);
    } 
    
    /**
     * Select song by id
     * @param int $iItemID
     * @return array
     */
    public function getItem($iItemID) {
        $this->arrValues = parent::getItem($iItemID);
    }
    
    /**
     * Save Item
     */
    public function save() {
        return parent::saveItem();
    }
    
    /**
     * Delete Item
     */
    public function delete($iItemID) {
        parent::delete($iItemID);
    } 
    
    /**
     * Get org name
     * @global obj $db
     * @param int $iOrgID
     * @return string vcOrgName
     */
    static function getname($iOrgID) {
        global $db;
        $params = array($iOrgID);
        $strSelect = "SELECT vcOrgName FROM org WHERE iOrgID = ?";
        return $db->_fetch_value($strSelect,$params);
    }
    
    /**
     * Select orgs for select option purposes
     * @return array Returns an array with the given data
     */
    public function getoptions() {
        $strSelect = "SELECT iOrgID, vcOrgName " . 
                        "FROM org " . 
                        "WHERE iDeleted = 0 " . 
                        "ORDER BY vcOrgName";
        return $this->db->_fetch_array($strSelect);
    }     
        
    
}

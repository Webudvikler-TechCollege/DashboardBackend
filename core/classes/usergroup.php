<?php
class usergroup extends crud {
    protected $dbTable = "usergroup";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();
    public $useTopics = 1;
    public $module = crud::MOD_USERGROUP;
    
    const GROUP_SYSADMIN = 1;
    const GROUP_ADMIN = 2;
    const GROUP_EXTRANET = 3;
    const GROUP_NEWSLETTER = 4;
    
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
     * Select item by id
     * @param int $iItemID
     * @return array
     */
    public function getItem($iItemID) {
        $this->arrValues = parent::getItem($iItemID);
    }
    
    /**
     * Method Save
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

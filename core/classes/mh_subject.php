<?php
class mh_subject extends crud {
    protected $dbTable = "mh_subject";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();

    public function __construct() {
        parent::__construct($this->dbTable);
    }
        
    /**
     * List newsletters
     * @return type
     */
    public function getAll() { 
        $strSelect = "SELECT * " . 
                        "FROM mh_subject " .
                        "WHERE iDeleted = 0 " . 
                        "ORDER BY vcName";
        return $this->db->_fetch_array($strSelect);
    } 
    
    /**
     * Select subject by id
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
     * Save item
     */
    public function save() {
        return parent::saveItem();
    }
    
    /**
     * Delete item
     */
    public function delete($iItemID) {
        parent::delete($iItemID);
    } 
    
}

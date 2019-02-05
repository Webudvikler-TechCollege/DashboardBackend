<?php
class mh_activity extends crud {
    protected $dbTable = "mh_activity";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();

    public function __construct() {
        parent::__construct($this->dbTable);
    }
        
    /**
     * Select matching rows from a specific query
     * @param string $strSelect
     * @return array Returns an array with the given data
     */
    public function getall() {
        $strSelect = "SELECT a.*, s.vcFriendlyName FROM mh_activity a " .
                        "LEFT JOIN mh_subject s " .
                        "ON a.vcSubject = s.vcName " .
                        "WHERE daTime > UNIX_TIMESTAMP() " .
                        "ORDER BY a.daTime";
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
}

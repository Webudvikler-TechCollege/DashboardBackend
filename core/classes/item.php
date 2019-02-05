<?php
class item extends crud {
    protected $dbTable = "item";
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
        $this->arrLabels = array(
            "opts" => "Options", 
            "itemtitle" => $this->arrColumns["itemtitle"]["Label"]
        );        
        $strSelect = "SELECT * FROM item WHERE iDeleted = 0 ORDER BY itemtitle";
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

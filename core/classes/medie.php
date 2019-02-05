<?php
class medie extends crud {
    protected $dbTable = "medie";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();

    public function __construct() {
        parent::__construct($this->dbTable);
	    $this->arrColumns["vcFile"]["Formtype"] = parent::INPUT_IMAGEEDITOR;
    }
        
    /**
     * List medias
     * @return type
     */
    public function getAll() { 
        $strSelect = "SELECT * " . 
                        "FROM medie " .
                        "WHERE iDeleted = 0 " . 
                        "ORDER BY vcTitle";
        return $this->db->_fetch_array($strSelect);
    } 
    
    /**
     * Select medie by id
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

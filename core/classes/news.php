<?php
class news extends crud {
    protected $dbTable = "news";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();

    public function __construct() {
        parent::__construct($this->dbTable);
        $this->arrColumns["txContent"]["Formtype"] = crud::INPUT_TEXTEDITOR;
	    $this->arrColumns["daStart"]["Formtype"] = parent::INPUT_DATETIME;
    }
        
    /**
     * List newsletters
     * @return type
     */
    public function getAll() { 
        $strSelect = "SELECT * " . 
                        "FROM news " .
                        "WHERE iDeleted = 0 " . 
                        "ORDER BY daCreated";
        return $this->db->_fetch_array($strSelect);
    } 
    
    /**
     * Select newsletter by id
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

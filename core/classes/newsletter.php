<?php
class newsletter extends crud {
    protected $dbTable = "newsletter";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();

    public function __construct() {
        parent::__construct($this->dbTable);
        $this->arrColumns["iMailIsSent"]["Formtype"] = crud::INPUT_HIDDEN;
        $this->arrColumns["iMailsOpened"]["Formtype"] = crud::INPUT_HIDDEN;
        $this->arrColumns["txContent"]["Formtype"] = crud::INPUT_TEXTEDITOR;
    }
        
    /**
     * List newsletters
     * @return type
     */
    public function getAll() { 
        $strSelect = "SELECT * " . 
                        "FROM newsletter " . 
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

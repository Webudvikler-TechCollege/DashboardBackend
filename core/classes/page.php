<?php
class page extends crud {
    protected $dbTable = "page";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();
    public $useTopics = 1;
    public $module = crud::MOD_ORG;
    

    public function __construct() {
        parent::__construct($this->dbTable, $this->useTopics, $this->module);
        $this->arrColumns["txContent"]["Formtype"] = parent::INPUT_TEXTEDITOR;
        $this->arrColumns["vcPageImage"]["Formtype"] = parent::INPUT_IMAGEEDITOR;
        $this->arrColumns["daStart"]["Formtype"] = parent::INPUT_DATETIME;
        $this->arrColumns["daStop"]["Formtype"] = parent::INPUT_DATETIME;
    }
        
    /**
     * Select matching rows from a specific query
     * @param string $strSelect
     * @return array Returns an array with the given data
     */
    public function listbyparent($iParentID) {  
        $this->arrLabels = array(
            "opts" => "Options", 
            "vcTitle" => $this->arrColumns["vcTitle"]["Label"],
            "daCreated" => $this->arrColumns["daCreated"]["Label"],
            "iSortNum" => $this->arrColumns["iSortNum"]["Label"]
        );        
        
        
        $params = array($iParentID);
        $strSelect = "SELECT iPageID, vcTitle, vcUrlName, daCreated, iIsStartPage, iSortNum, iIsActive " . 
                        "FROM page " . 
                        "WHERE iParentID = ? " .
                        "AND iDeleted = 0 " . 
                        "ORDER BY iSortNum ASC";
        $rows = $this->db->_fetch_array($strSelect, $params);
        foreach ($rows as $key => $value) {
            $rows[$key]["vcUrlName"] .= ".htm";
        }
        return $rows;
    } 
    
    /**
     * Select all pages
     * @return array Returns an array with the given data
     */
    public function getall() {  
        $strSelect = "SELECT iPageID, iParentID, vcTitle, vcUrlName, iIsStartPage, iSortNum, iIsActive " . 
                        "FROM page " . 
                        "WHERE iIsActive = 1 " .
                        "AND iDeleted = 0";
        $rows = $this->db->_fetch_array($strSelect);
        foreach ($rows as $key => $value) {
            $rows[$key]["vcUrlName"] .= ".htm";
        }
        return $rows;
    }    
    
    /**
     * Select page by id
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
     * Save page
     * @return int iPageID
     */
    public function save() {                
        return parent::saveItem();
    }
    
    /**
     * Creates a page internally
     * Used to create startpages along with folder creation
     * @return int iPageID
     */
    public function create() {                
        return parent::createItem();
    }    
    
    /**
     * Delete a page
     */
    public function delete($iItemID) {
        parent::delete($iItemID);
    } 
    
    /**
     * Returns sort number for new items
     * @param int $iParentID
     * @return int iSortNum
     */
    public function getNewSortNum($iParentID) {
        $params = array($iParentID);
        $sql = "SELECT MAX(iSortNum)+1 FROM page WHERE iParentID = ?";
        return $this->db->_fetch_value($sql,$params);
    }
}

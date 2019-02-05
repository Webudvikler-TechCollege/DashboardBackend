<?php
class topicgroup extends crud {
    protected $dbTable = "topicgroup";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();

    public function __construct() {
        parent::__construct($this->dbTable);
    }
        
    /**
     * List topics by topic group name
     * @param type $iTopicGroupID
     * @return type
     */
    public function getall() { 
        $this->arrLabels = array(
            "opts" => "Options",
            "vcTitle" => $this->arrColumns["vcTitle"]["Label"]
        );
        
        $strSelect = "SELECT * FROM topicgroup WHERE iDeleted = 0";
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

<?php
class topic extends crud {
    protected $dbTable = "topic";
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
    public function listbyparent($iTopicGroupID) { 
        $params = array($iTopicGroupID);

        $strSelect = "SELECT * " . 
                        "FROM topic " . 
                        "WHERE iTopicGroupID = ? " . 
                        "AND iDeleted = 0";
        return $this->db->_fetch_array($strSelect,$params);
    } 
    
    /**
     * List topics by type and element id's from topicrel
     * @param int $iElementID
     * @param int $iType
     * @return array
     */
    public function listtopicrel($iElementID, $iType) { 
        $params = array($iElementID, $iType);

        $strSelect = "SELECT t.iTopicID, t.vcTopicName " . 
                        "FROM topic t, topicrel x " . 
                        "WHERE x.iElementID = ? " . 
                        "AND x.iType = ? " . 
                        "AND x.iTopicID = t.iTopicID " . 
                        "AND t.iDeleted = 0";
        return $this->db->_fetch_array($strSelect,$params);
    }     
    
    /**
     * Select topic by id
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

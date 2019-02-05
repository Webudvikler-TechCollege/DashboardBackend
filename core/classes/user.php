<?php
class user extends crud {
    protected $dbTable = "user";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();
    public $useTopics = 1;
    public $module = crud::MOD_USER;
    
    public function __construct() {
        parent::__construct($this->dbTable, $this->useTopics, $this->module);
        $this->arrColumns["vcImage"]["Formtype"] = parent::INPUT_IMAGEEDITOR;
    }

    /**
     * Select matching rows from a specific query
     * @return array Returns an array with the given data
     */
    public function getall() {
        $strSelect = "SELECT * FROM user WHERE iDeleted = 0 ORDER BY vcLastName, vcFirstName";
        return $this->db->_fetch_array($strSelect);
    } 
    
    /**
     * Select users for select option purposes
     * @return array Returns an array with the given data
     */
    public function getoptions() {
        $strSelect = "SELECT iUserID, CONCAT(vcFirstName, ' ', vcLastName) AS vcName " . 
                        "FROM user " . 
                        "WHERE iDeleted = 0 " . 
                        "ORDER BY vcLastName, vcFirstName";
        return $this->db->_fetch_array($strSelect);
    }

    /**
     * Select user by id
     * @param $iItemID
     * @return void
     */
    public function getItem($iItemID) {
        $this->arrValues = parent::getItem($iItemID);

        if($this->arrValues["iOrgID"] > 0) {
            $org = new Org();
            $org->getItem($this->arrValues["iOrgID"]);
            $this->arrValues["org"] = $org->arrValues;
        }
        
        $this->arrValues["arrUserGroups"] = $this->getgrouprelations();
        
        foreach($this->arrValues as $key => $value) {
            $this->$key = $value;
        }        

        foreach($this->arrValues["arrUserGroups"] as $value) {
            $role = strtolower($value["vcRoleName"]);
            $this->$role = 1;
        }

    }

    /**
     * 
     * @global type $db
     * @param type $iItemID
     * @return type
     */
    static function getname($iItemID) {
        global $db;
        $params = array($iItemID);
        $strSelect = "SELECT CONCAT(vcFirstName,' ',vcLastName) FROM user WHERE iUserID = ?";
        return $db->_fetch_value($strSelect,$params);
    }     
    
    /**
     * 
     * @return type
     */
    public function getgrouprelations() {
        $params = array($this->arrValues["iUserID"]);
        $strSelect = "SELECT g.iGroupID, g.vcGroupName, g.vcRoleName " . 
                        "FROM usergroup g, usergrouprel x " . 
                        "WHERE x.iUserID = ? " . 
                        "AND x.iGroupID = g.iGroupID " . 
                        "AND g.iDeleted = 0";
        return $this->db->_fetch_array($strSelect, $params);        
    }
    
    /**
     * 
     */
    public function save() {
        //showme($_POST["iUserRole"]);
        
        $args = array(
                    "iUserRole" => array(
                        "filter" => FILTER_VALIDATE_INT,
                        "flags" => FILTER_REQUIRE_ARRAY
                        )
                    );
        $arrRoles = filter_input_array(INPUT_POST, $args);
        
        if(count($arrRoles["iUserRole"])) {
            $this->arrColumns["iUserRole"]["Value"] = array_sum($arrRoles["iUserRole"]);
        }
        
        $iUserID = filter_input(INPUT_POST, "iUserID", FILTER_VALIDATE_INT);
        $strPassword = filter_input(INPUT_POST, "vcPassword", FILTER_SANITIZE_STRING);
        
        if($iUserID > 0 && empty($strPassword)) {
            unset($this->arrColumns["vcPassword"]);
        } else {
            $this->arrColumns["vcPassword"]["Value"] = md5($strPassword);
        }
        
        return parent::saveItem();
    }
    
    /**
     * Method Delete
     */
    public function delete($iItemID) {
        parent::delete($iItemID);
    }    
    
}

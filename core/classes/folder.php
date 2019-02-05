<?php
class folder extends crud {
    protected $dbTable = "folder";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();
    public $arrParents = array();
    public $arrPaths = array();

    public function __construct() {
        parent::__construct($this->dbTable);
    }
        
    /**
     * Get all folders 
     * @return array Returns an array with the given data
     */
    public function getall() {
        $page = new page();
        $strSelect = "SELECT f.* FROM folder f, page p " .
                        "WHERE f.iFolderID = p.iParentID " . 
                        "AND p.iIsStartPage = 1 " . 
                        "AND p.iIsActive = 1 " . 
                        "AND f.iDeleted = 0";
        $rows = $this->db->_fetch_array($strSelect);
        foreach ($rows as $key => $value) {
            $rows[$key]["vcUrlName"] = $this->getUrlPath($value["iFolderID"]);
            $rows[$key]["arrPages"] = $page->listbyparent($value["iFolderID"]);
            $rows[$key]["iStartPageID"] = self::getStartPage($rows[$key]["arrPages"]);
        }
        return $rows;
    } 

    /**
     * Select matching rows from a specific query
     * @param string $strSelect
     * @return array Returns an array with the given data
     */
    public function getallByParent($iParentID) {
        $params = array($iParentID);
        $strSelect = "SELECT * FROM " . $this->dbTable . " " .
                        "WHERE iParentID = ? " . 
                        "AND iDeleted = 0";
        return $this->db->_fetch_array($strSelect, $params);
    } 
        
    /**
     * Select folder by id
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
     * Save folder
     * @return int iFolderID
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
    
    /**
     * Returns a structured array with folder options
     * Used for parent folder select options 
     * @global object $db
     * @param int $iParentID
     * @param array $arrFolderOpts
     * @param string $arrExclIds
     * @param string $strPadding
     * @return array
     */
    static function getFolderOpts($iParentID, &$arrFolderOpts, $arrExclIds = array(), $strPadding = "&nbsp;&nbsp;") {
        global $db;
        
        if(count($arrFolderOpts) == 0) {
            $arrFolderOpts[] = array(-1, "Website Root");
        }

        $params = array($iParentID);
        $strSelect = "SELECT iFolderID, vcTitle FROM folder " .
                        "WHERE iParentID = ? ";
        if (count($arrExclIds) > 0) {
            $strSelect .= "AND (iFolderID NOT IN(" . implode(",", $arrExclIds) . ")) ";
        }
        $strSelect .= "AND iDeleted = 0 ORDER BY iSortNum";
        $row = $db->_fetch_array($strSelect, $params);
        foreach ($row as $key => $values) {
            $arrFolderOpts[] = array($values["iFolderID"], $strPadding . $values["vcTitle"]);
            self::getFolderOpts($values["iFolderID"], $arrFolderOpts, $arrExclIds, $strPadding . "&nbsp;&nbsp;");
        }
        return $arrFolderOpts;
    }
    
    /**
     * Returns the folder name
     * @global object $db
     * @param int $iFolderID
     * @return string 
     */
    static function getfoldername($iFolderID) {
        global $db;
        if($iFolderID > 0) { 
            $params = array($iFolderID);
            $strSelect = "SELECT vcTitle FROM folder WHERE iFolderID = ?";
            $name = $db->_fetch_value($strSelect,$params);
        } else {
            $name = "Website Root";
        }
        return $name;
    }
    
    /**
     * Returns an array with parent Id's
     * @param int $iFolderID
     * @param array $arrParents
     */
    public function getParents($iFolderID, &$arrParents = array()) {
        
        $param = array($iFolderID);
        $strSelectParents = "SELECT iParentID, vcUrlName FROM " . $this->dbTable . " " .
                                "WHERE iFolderID = ?";
        $row = $this->db->_fetch_array($strSelectParents,$param);
        
        if(count($row)) {
            foreach($row as $key => $values) {
                array_unshift($this->arrParents,$values["iParentID"]);
                array_unshift($this->arrPaths,$values["vcUrlName"]);
                $this->getParents($values["iParentID"],$arrParents);
            }
        }
    }  
    
    /**
     * 
     * @param int $iFolderID
     * @return string Url Path
     */
    public function getUrlPath($iFolderID) {
        $this->arrPaths = array();
        $this->getParents($iFolderID);
        return "/" . implode("/", $this->arrPaths);
        
    }
    
    /**
     * Returns sort number for new items
     * @param int $iParentID
     * @return int iSortNum
     */
    public function getNewSortNum($iParentID) {
        $params = array($iParentID);
        $sql = "SELECT MAX(iSortNum) FROM folder WHERE iParentID = ?";
        $newSortNum = $this->db->_fetch_value($sql,$params);
        return ($newSortNum) ? substr($newSortNum, strlen($newSortNum)-2, 1)+1 . 0 : 10;
    }   
    
    /**
     * Returns the startpage ID 
     * @param array $arrPages
     * @return int iPageID
     */
    static function getStartPage($arrPages) {
        foreach($arrPages as $arrValues) {
            if($arrValues["iIsStartPage"] > 0) {
                return $arrValues["iPageID"];
            }
        }
    }
    
}

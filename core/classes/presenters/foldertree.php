<?php
class FolderTree {

    public $arrItems = array();
    public $arrParents;
    public $iParentID;
    public $strKeyName;
    public $dbTable;
    public $strModePrepend;
    public $strRootTitle;
    public $accHtml;

    protected $db;
    
    public function __construct($iParentID,$dbTable,$strRootTitle = "Root", $strModePrepend = "folder") {
        global $db;
        $this->db = $db;
         
        $this->iParentID = $iParentID;
        $this->dbTable = $dbTable;
        $this->strModePrepend = $strModePrepend;
        $this->strRootTitle = $strRootTitle;
        $this->strKeyName = $this->getKeyName();
        $this->arrItems = $this->getItems();
        $strParents = $this->getParents($this->iParentID);
        $this->arrParents = explode(",",$strParents);
    }
    
    protected function getItems() {
        $this->arrItems = array();
        $strSelect = "SELECT * FROM " . $this->dbTable . " " . 
                        "WHERE iDeleted = 0 " . 
                        "AND iParentID = -1 " .
                        "ORDER BY iSortNum ASC";
        $row = $this->db->_fetch_array($strSelect);
        if(count($row) > 0) {
            foreach ($row as $key => $values) {
                $this->arrItems[] = $values;
            }
        }
        return $this->arrItems;        
        
    }
    
    protected function getKeyName() {
        $strSelect = "SHOW INDEX FROM " . $this->dbTable . " WHERE Key_name = 'PRIMARY'";
        $row = $this->db->_fetch_array($strSelect);
        return $row[0]["Column_name"];
    }
    
    protected function getParents($iParentID) {
        $param = array($iParentID);
        $strSelectParents = "SELECT iParentID FROM " . $this->dbTable . " " .
                            "WHERE ".$this->strKeyName." = ?";
        $row = $this->db->_fetch_array($strSelectParents,$param);
        $numResult = count($row);
        $strValues = "";

        if ($numResult) {
            foreach($row as $key => $values) {
                $thisParent = $values["iParentID"];
                $parents = $values["iParentID"] > 0 ? "," . $this->getParents($thisParent) : "";
                $strValues = "$thisParent$parents";
            }
        } else {
            $strValues = "";
        }
        return $strValues;
    }
    
    public function buildTree() {
        $rootstate = (!$this->iParentID) ? "selected" : "";
        $this->accHtml = "<div id=\"menutree\" data-mode=\"".$this->strModePrepend."\">\n" . 
                        "    <ol class=\"tree\">\n" . 
                        "        <li id=\"root\">\n" . 
                        "            <a class=\"isparent\" class=\"".$rootstate."\" href=\"?mode=list\">" . $this->strRootTitle . "</a>\n" . 
                        "            <ol>\n";
        
        foreach($this->arrItems as $key => $arrItemInfo) {

            $strActiveClass = ($arrItemInfo["iIsActive"] === 0) ? "inactive" : "";
            
            if(in_array($arrItemInfo[$this->strKeyName],$this->arrParents) || 
                    $arrItemInfo[$this->strKeyName] == $this->iParentID) {

                $strClass = ($this->iParentID == $arrItemInfo[$this->strKeyName]) ? " selected " : "";

                $this->accHtml .= "\t\t\t\t<li><a id=\"".$arrItemInfo[$this->strKeyName]."\" class=\"isparent ".$strClass. " " . $strActiveClass . "\" " . 
                                        "href=\"?mode=list&iParentID=".$arrItemInfo[$this->strKeyName]."\">" . $arrItemInfo["vcTitle"] . "</a>\n"; 
                $this->getMenu($arrItemInfo[$this->strKeyName],$this->accHtml, $arrItemInfo["iIsActive"]); 
            } else {
                $this->accHtml .= "\t\t\t\t<li><a id=\"".$arrItemInfo[$this->strKeyName]."\" href=\"?mode=list&iParentID=".$arrItemInfo[$this->strKeyName]."\" class=\"".$strActiveClass."\">" . 
                                $arrItemInfo["vcTitle"] . "</a>\n"; 
            }
            $this->accHtml .= "\t\t\t\t</li>\n";
        }
        $this->accHtml .= "\t\t\t</ol>\n\t\t</li>\n\t</ol>\n</div>\n" . 
                            "<ol id=\"folderopts\">\n" . 
                            "    <li><a><span class=\"fa fa-pencil\"></span>Rediger</a></li>\n" . 
                            "    <li><a><span class=\"fa fa-eye\"></span>Detaljer</a></li>\n" . 
                            "    <li><a><span class=\"fa fa-star\"></span>Opret</a></li>\n" . 
                            "    <li><a><span class=\"fa fa-trash-o\"></span>Slet</a></li>\n" . 
                            "</ol>\n\n";
        return $this->accHtml;        
    }
    
    protected function getMenu($iParentID, &$accHtml, $parentIsActive = 0) {
        $this->iParentID = $iParentID;
        $this->accHtml = $accHtml;

        $iCurParentID = filter_input(INPUT_GET,"iParentID",FILTER_SANITIZE_NUMBER_INT, getDefaultValue(-1));

        $params = array($this->iParentID);
        $strSelect = "SELECT " . $this->strKeyName . ", iParentID, vcTitle, iIsActive, iSortNum FROM " . $this->dbTable . " " . 
                        "WHERE iParentID = ? " .
                        "AND iDeleted = 0 " .
                        "ORDER BY iSortNum";
        $row = $this->db->_fetch_array($strSelect,$params);
        
        if(count($row) > 0) {

            $this->accHtml .= "\t\t\t\t<ol>\n";

            foreach($row as $arrItemInfo) {

                $strActiveClass = ($parentIsActive === 0) ? "inactive" : 
                        ($arrItemInfo["iIsActive"] === 0) ? "inactive" : "";

                $strClass = ($iCurParentID == $arrItemInfo[$this->strKeyName]) ? " selected " : "";

                if((in_array($arrItemInfo["iParentID"],$this->arrParents) && $iCurParentID == $arrItemInfo[$this->strKeyName]) || 
                        in_array($arrItemInfo[$this->strKeyName],$this->arrParents)) {
                    $this->accHtml .= "\t\t\t\t\t<li><a  id=\"".$arrItemInfo[$this->strKeyName]."\" class=\"isparent ".$strClass. " " . $strActiveClass . "\" href=\"?mode=list&iParentID=" . 
                                        $arrItemInfo[$this->strKeyName]."\">" . $arrItemInfo["vcTitle"] . "</a>\n"; 
                    $this->getMenu($arrItemInfo[$this->strKeyName],$this->accHtml); 
                    $this->accHtml .= "</li>\n";
                } else {
                    $this->accHtml .= "\t\t\t\t\t<li><a  id=\"".$arrItemInfo[$this->strKeyName]."\" href=\"?mode=list&iParentID=".$arrItemInfo[$this->strKeyName]."\" class=\"".$strActiveClass."\">" . $arrItemInfo["vcTitle"] . "</a></li>\n"; 
                }
            }
            $this->accHtml .= "\t\t\t\t</ol>\n";
        }
        return $this->accHtml;        
    }
}
?>
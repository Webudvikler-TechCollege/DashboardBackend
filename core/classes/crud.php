<?php
/**
 * Abstract class for CRUD db handles
 * @author Heinz K, Nov 2016
 * 
 * Initializes a table
 */
abstract class crud {
    
    protected $db;
    private $dbTable;
    private $primaryKey;
    protected $arrColumns = array();
    protected $arrValues = array();
    protected $useTopics = 0;
    protected $module = 0;
    
    /* Form Input Constants */
    const INPUT_HIDDEN = "hidden";
    const INPUT_TEXT = "text";
    const INPUT_TEXT_READONLY = "readonly";
    const INPUT_EMAIL = "email";
    const INPUT_PASSWORD = "password";
    const INPUT_TEXTAREA = "textarea";
    const INPUT_TEXTEDITOR = "texteditor";
    const INPUT_SELECT = "select";  
    const INPUT_CHECKBOX = "checkbox";  
    const INPUT_CHECKBOXMULTI = "checkboxmulti";  
    const INPUT_RADIO = "radio";
    const INPUT_FILE = "file";
    const INPUT_DATE = "date";
    const INPUT_DATETIME = "datetime";
    const INPUT_IMAGEEDITOR = "imageeditor";
    const INPUT_FILEEDITOR = "fileeditor";
    const INPUT_CODEEDITOR = "codeeditor";

    /* Module Type Constants */
    const MOD_CORE = 1;
    const MOD_USER = 2;
    const MOD_USERGROUP = 3;
    const MOD_ORG = 4;
    const MOD_FOLDER = 5;
    const MOD_PAGE = 6;
    const MOD_EVENT = 7;      
    const MOD_WEBSHOP = 8;      
    
    /**
     * Constructor sets db object and calls for table initialize
     * @global object $db
     * @param string $dbTable
     */
    protected function __construct($dbTable, $useTopics = 0, $module = 0) {
        global $db;
        $this->db = $db;
        $this->dbTable = $dbTable;
        $this->useTopics = $useTopics;
        $this->module = $module;
        $this->initTable();
    }
    
    /**
     * Sets table name and calls for fields and primary key methods
     * @param string $dbTable
     */
    private function initTable() {
        
        $sql = "SHOW FULL COLUMNS FROM " . $this->dbTable;
        $rows = $this->db->_fetch_array($sql);
        
        foreach($rows as $arrFieldValues) {
            if($arrFieldValues["Field"] === "iDeleted") { continue; }
            
            if($arrFieldValues["Key"] === "PRI") {
                $this->primaryKey = $arrFieldValues["Field"];
                $arrFieldValues["Default"] = -1;
            }
            
            $this->arrColumns[$arrFieldValues["Field"]] = array(
                "Datatype" => onlyLetters($arrFieldValues["Type"]),
                "Formtype" => $this->getFormType($arrFieldValues),
                "Filter" => $this->getFilterType($arrFieldValues["Type"]),
                "Key" => $arrFieldValues["Key"],
                "Required" => ($arrFieldValues["Null"] === "NO") ? 1 : 0,
                "Default" => $arrFieldValues["Default"],
                "Label" => $arrFieldValues["Comment"],
                "Value" => $arrFieldValues["Default"]
            );
            /* Fix arrValues with default values */
            $this->arrValues[$arrFieldValues["Field"]] = $this->arrColumns[$arrFieldValues["Field"]]["Default"];
        }
        
        if($this->useTopics) {
            $this->arrColumns["topics"] = array("Label" => "Emnetags");
            $this->arrValues["topics"] = array();
        }
    }
            
    /**
     * Select a single row
     * @param int $iItemID
     * @return array Returns an array with column names and values
     */
    protected function getItem($iItemID) {
        $params = array($iItemID);
        $row = array();
        $strSelect = "SELECT * FROM " . $this->dbTable . " WHERE " . $this->primaryKey . " = ?";
        if($row = $this->db->_fetch_array($strSelect,$params)) {
            $row = call_user_func_array('array_merge', $row);
            foreach($this->arrColumns as $key => $value) {
                if(isset($row[$key])) {
                    $this->arrColumns[$key]["Value"] = $row[$key];
                }
            }

            if($this->useTopics) {
                $topic = new topic;
                $topics = $topic->listtopicrel($iItemID, $this->module);
                $row["topics"] = implode("<br>", array_column($topics, "vcTopicName"));
            }
        }
        return $row;
    }
    
    /**
     * Add Post Var values to arrColumns
     */
    protected function setItem() {
        foreach($this->arrColumns as $key => $value) {
            if(filter_input(INPUT_POST, $key, $value["Filter"])) {
                $this->arrColumns[$key]["Value"] = filter_input(INPUT_POST, $key, $value["Filter"]);
            }
        }        
    }    
    
    /**
     * Filters post vars and determines create or update state
     * from primary key value
     * @return method
     */
    protected function saveItem() {        
        foreach($this->arrColumns as $key => $value) {
            if(isset($value["Datatype"])) {
                if(filter_input(INPUT_POST, $key, $value["Filter"])) {
                    $this->arrColumns[$key]["Value"] = filter_input(INPUT_POST, $key, $value["Filter"]);
                }
            }
        }
        if($this->arrColumns[$this->primaryKey]["Value"] > 0) {
            return $this->updateItem();
        } else {
            return $this->createItem();
        }
    }
    
    /**
     * Update item
     * @return int Returns primary key
     */
    private function updateItem() {
        $arrValues = $this->prepareArray();
        $arrParams = array_values($arrValues);
        $arrParams[] = $this->arrColumns[$this->primaryKey]["Value"];
        $sql = "UPDATE " . $this->dbTable . " SET " . 
                implode(array_keys($arrValues), " = ?, ") . " = ? " . 
                "WHERE " . $this->primaryKey . " = ?";
        $this->db->_query($sql,$arrParams);
        return $this->arrColumns[$this->primaryKey]["Value"];
    }
    
    /**
     * Create item
     */
    protected function createItem() {
        $arrValues = $this->prepareArray();
        if(key_exists("daCreated", $arrValues)) {
            $arrValues["daCreated"] = time();
        }
        $arrParamBinds = array_fill(0,count($arrValues),"?");
        $sql = "INSERT INTO " . $this->dbTable . " (" .
                    implode(array_keys($arrValues), ",") . ") " . 
                    "VALUES(" . implode($arrParamBinds, ",") . ")";
        $this->db->_query($sql, array_values($arrValues));
        //echo $this->db->_toString($sql, array_values($arrValues));
        return $this->db->_getinsertid();
    }
    
    /**
     * Extracts keys and value from arrColumns and
     * comines a new array without the primary key
     * @return array Returns combined array
     */
    private function prepareArray() {
        foreach($this->arrColumns as $key => $value) {
            if(isset($value["Datatype"])) {
                $arrValues[$key] = $value["Value"];
            }
        }
        unset($arrValues[$this->primaryKey]);
        return $arrValues;
    }
    
    /**
     * 
     * @param type $arrValues
     * @return type
     */
    private function getFormType($arrValues) {
        $type = onlyLetters($arrValues["Type"]);
        $type = ($type === "bigint") ? "select" : $type;
        $type = ($arrValues["Key"] === "PRI") ? "identity" : $type;
        $type = ($type === "tinyint") ? "checkbox" : $type;
        $type = ($arrValues["Field"] === "vcEmail") ? "email" : $type;
        $type = ($arrValues["Field"] === "vcPassword") ? "password" : $type;
        $type = ($arrValues["Field"] === "daCreated") ? "hidden" : $type;
        
        switch(strtoupper($type)) {
            default:
            case "IDENTITY":
            case "HIDDEN":
                $formtype = self::INPUT_HIDDEN;
                break;
            case "VARCHAR":
            case "MEDIUMINT":
            case "INT":
            case "DOUBLE":
                $formtype = self::INPUT_TEXT;
                break;
            case "EMAIL":
                $formtype = self::INPUT_EMAIL;
                break;
            case "PASSWORD":
                $formtype = self::INPUT_PASSWORD;
                break;
            case "TEXT":
                $formtype = self::INPUT_TEXTAREA;
                break;
            case "ENUM":
            case "SELECT":
                $formtype = self::INPUT_SELECT;
                break;
            case "CHECKBOX":
                $formtype = self::INPUT_CHECKBOX;
                break;
            case "RADIO":
                $formtype = self::INPUT_RADIO;
                break;
        }
        return $formtype;
    }
    
    /**
     * 
     * @param type $value
     * @return int Returns type matching filter
     */
    protected function getFiltertype($value) {
        $type = onlyLetters($value);
        switch(strtoupper($type)) {
            default:
            case "TEXT":
            case "BLOB":
                $filter = FILTER_DEFAULT;
                break;
            case "VARCHAR":
                $filter = FILTER_SANITIZE_STRING;
                break;
            case "INT":
            case "TINYINT":
            case "SMALLINT":
            case "MEDIUMINT":
            case "BIGINT":
                $filter = FILTER_VALIDATE_INT;
                break;
            case "DOUBLE":
            case "FLOAT":
                $filter = FILTER_VALIDATE_FLOAT;
                break;
        }
        return $filter;
    }

    /**
     * Method Delete
     */
    protected function delete($iItemID) {
        $params = array($iItemID);
        $sql = "UPDATE " . $this->dbTable . " SET iDeleted = 1 WHERE " . $this->primaryKey . " = ?";
        $this->db->_query($sql, $params);
    }
}
<?php
class design extends crud {
    protected $dbTable = "design";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();
    public $arrTmpls = array();
    protected $filepath = COREPATH . "userapp/";

    public function __construct() {
        parent::__construct($this->dbTable);
        
        $this->arrTmpls = array(
                            "MAIN" => array("inc", file_get_contents(COREPATH . "templates/master.tmpl")),
                            "CSS" => array("css", file_get_contents(COREPATH . "templates/css.tmpl")),
                            "JS" => array("js",file_get_contents(COREPATH . "templates/js.tmpl")),
                            "APP" => array("php",file_get_contents(COREPATH . "templates/app.tmpl")),
                            //"TMPL" => array("tmpl",file_get_contents(COREPATH . "templates/tmpl.tmpl"))
                            );                
    }
        
    /**
     * List topics by topic group name
     * @param type $iTopicGroupID
     * @return type
     */
    public function getAll() { 
        $this->arrLabels = array(
            "opts" => "Options",
            "vcTitle" => $this->arrColumns["vcTitle"]["Label"]
        );        
        
        $strSelect = "SELECT * " . 
                        "FROM design " . 
                        "WHERE iDeleted = 0";
        return $this->db->_fetch_array($strSelect);
    } 
    
    /**
     * Select song by id
     * @param int $iItemID
     * @return array
     */
    public function getItem($iItemID) {
        $this->arrValues = parent::getItem($iItemID);
        
        $this->arrValues["files"] = array();
        $list = new designfile();
        $files = $list->listbyparent($iItemID);
        
        foreach ($files as $key => $value) {
            if(!isset($this->arrValues["files"][$value["vcFileType"]])) {
                $this->arrValues["files"][$value["vcFileType"]] = array();
            }
            $this->arrValues["files"][$value["vcFileType"]][] = $value["vcFileName"];
            
        }
        foreach ($this->arrValues as $key => $value) {
            $this->$key = $value;
        }
    }
    
    /**
     * 
     */
    static function getname($iItemID) {
        global $db;
        $params = array($iItemID);
        $strSelect = "SELECT vcTitle FROM design WHERE iDesignID = ?";
        return $db->_fetch_value($strSelect,$params);
    }    
    
    /**
     * Save Function
     */
    public function save() {
        $iDesignID = parent::saveItem();

        if($this->arrColumns["iDesignID"]["Value"] < 0) {
            /* Create app folder */
            $this->filepath .= "userapp/app" . $iDesignID;
            
            if(!mkdir($this->filepath)) {
                echo "failed to make app dir!";
            } else {
                foreach($this->arrTmpls as $key => $arrValues) {
                    $file = new designfile();
                    $filename = (($key === "MAIN") ? "master" : "app" . $iDesignID) . "." . $arrValues[0];
                    $file->arrColumns["iDesignID"]["Value"] = $iDesignID;
                    $file->arrColumns["vcFileType"]["Value"] = $key;
                    $file->arrColumns["vcFileName"]["Value"] = $filename;
                    $file->arrColumns["txContent"]["Value"] = str_replace("@CREATEDATE@", date("d. M Y H:i",time()), $arrValues[1]);
                    $file->arrColumns["daCreated"]["Value"] = time();
                    $file->save();
                }
            }
        }
        return $iDesignID;
    }
    
    /**
     * Method Delete
     */
    public function delete($iItemID) {
        parent::delete($iItemID);
    } 
    
}

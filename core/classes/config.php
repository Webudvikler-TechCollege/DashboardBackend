<?php
class config extends crud {
    protected $dbTable = "config";
    public $arrColumns = array();
    public $arrLabels = array();
    public $arrValues = array();
    
    public function __construct() {
        parent::__construct($this->dbTable);
        $this->getallByModule(1);
    }
        
    /**
     * Select matching rows from a specific query
     * @param string $strSelect
     * @return array Returns an array with the given data
     */
    public function getallByModule($iModule) {     
        $params = array($iModule);
        $strSelect = "SELECT * " . 
                        "FROM " . $this->dbTable . " " . 
                        "WHERE iModule = ?";
        $configrows = $this->db->_fetch_array($strSelect, $params);
        
        $row = array();
        $this->arrColumns = array();
        $this->arrLabels = array_combine(array_column($configrows, "vcVarName"),
                                array_column($configrows, "vcFriendlyName"));
        
        foreach($this->arrLabels as $key => $value) {
            $pointer = array_search($key, array_column($configrows, "vcVarName"));
            
            /*
            echo "<hr>";
            echo $configrows[$pointer]["vcVarName"];
            echo getAbbr($configrows[$pointer]["vcVarName"]);
            echo "<hr>";
             * 
             */
            switch(getAbbr($configrows[$pointer]["vcVarName"])) {
                case "i":
                    $datafield = "iIntVar";
                    $datatype = "bigint";
                    $formtype = "select";
                    //$val = ($configrows[$pointer]["iIntVar"] > 0) ? $rows[$pointer]["iIntVar"] : "";
                    break;
                case "vc":
                    //$val = $configrows[$pointer]["vcVarcharVar"];
                    $datafield = "vcVarName";
                    $datatype = "varchar";
                    $formtype = "text";
                    break;
                case "tx":
                    //$val = $configrows[$pointer]["txTextVar"];
                    $datafield = "txTextVar";
                    $datatype = "text";
                    $formtype = "textarea";
                    break;
                    
            }
            $row[] = array(
                            "vcVarName" => $configrows[$pointer]["vcVarName"],
                            "vcFriendlyName" => $configrows[$pointer]["vcFriendlyName"],
                            "value" => $configrows[$pointer][$datafield]
                        );
            
            $this->arrValues[$configrows[$pointer]["vcVarName"]] = $configrows[$pointer][$datafield];
            
            $this->arrColumns[$configrows[$pointer]["vcVarName"]] = array(
                "Datafield" => $datafield,
                "Datatype" => $datatype,
                "Formtype" => $formtype,
                "Filter" => parent::getFiltertype($datatype),
                "Required" => 0,
                "Label" => $value
            );
            
        }
        return $row;
    } 
    
    /**
     * Save config settings
     */
    public function save() {
        foreach($this->arrColumns as $key => $value) {
            if(filter_input(INPUT_POST, $key, $value["Filter"])) {
                $this->arrColumns[$key]["Value"] = filter_input(INPUT_POST, $key, $value["Filter"]);
            }
            
            $params = array($this->arrColumns[$key]["Value"],$key);
            $strUpdate = "UPDATE " . $this->dbTable . " SET " . $value["Datafield"] . " = ? " . 
                            "WHERE vcVarName = ?";
            
            $this->db->_query($strUpdate,$params);
        }
    }
    
    /**
     * Creates an array with config settings
     * @param type $moduleid
     * @return type
     */
    public function createArray($moduleid = crud::MOD_CORE) {
        $arrConfig = array();
        $params = array($moduleid);
        $strSelect = "SELECT * FROM config WHERE iModule = ?";
        $row = $this->db->_fetch_array($strSelect,$params);
        foreach($row as $key => $arrValues) {
            switch(getAbbr($arrValues["vcVarName"])) {
                case "i":
                    $arrConfig[$arrValues["vcVarName"]] = $arrValues["iIntVar"];
                    break;
                case "vc":
                    $arrConfig[$arrValues["vcVarName"]] = $arrValues["vcVarcharVar"];
                    break;
                case "tx":
                    $arrConfig[$arrValues["vcVarName"]] = $arrValues["txTextVar"];
                    break;
            }
        }
        return $arrConfig;
    }    
}

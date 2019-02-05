<?php
/**
 *  
 */
class Validate {
    public $strFormId;
    public $strErrMsg = '';
    public $arrFields;
    public $accJS = '';
    public $strName;
    
    public function __construct($strName) {
        $this->strName = $strName;
    }

    public function setValidate() {
        $this->accJS = "<script>\n" .
                        "function " . $this->strName . "() {\n\t" . 
                        "var result = 1;\n";

        if(count($this->arrFields) > 0) {
            foreach($this->arrFields as $key => $arrValues) {
                if($arrValues[1] === "hidden") { continue; }

                switch(strtoupper($arrValues[1])) {
                    case "TEXT":
                    case "TEXTAREA":
                    default:
                        $this->strErrMsg = "Du skal udfylde feltet " . strtolower($arrValues[0]);
                        break;
                    case "SELECT":
                        $this->strErrMsg = "Du skal vælge en " . strtolower($arrValues[0]);
                        break;
                }
                $this->accJS .= "\n\tif(!$(\"#".$key."\").val()) {\n\t\t" .
                                    "result = 0;\n\t\t" . 
                                    "fielderror('".$key."','".$this->strErrMsg."','".$arrValues[1]."');\n\t\t" . 
                                    "return false;\n\t" . 
                                    "}\n\n\t";
            }
        }
        $this->accJS .= "if(result) {\n\t" . 
                        "   $('#".$this->strFormId."').submit();\n\t" .
                        "}\n" . 
                        "}\n" .
                        "</script>\n";
        
        
        return $this->accJS;
    }
}
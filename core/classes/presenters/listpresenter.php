<?php
/**
 * Description of List Presenter
 * 
 * A class for element listing
 * Requires an array of fieldnames (Captions) for 
 * a horizontal column list with a key matching array 
 * of data for a vertical row list.
 * Optional sorting settings
 * 
 * @author Heinz K, Nov 2016
 * 
 * @param array arrLabels Array with db fieldname as key and friendly name as value 
 * @param array arrValues Multiple array with db fieldname as key with matching db values 
 * @param array arrOrderOpts Array with field names for list ordering options
 * @param bool hasSortOnSave A boolean flag to enable sort number savings on list
 * @param string strListClass Option to inject a custom class to the list table
 * @param string accHtml Accumulated html with list output
 * 
 */
class listPresenter {
    public $arrLabels;
    public $arrValues;    
    public $arrOrderOpts;
    public $hasSortOnSave;
    public $strListClass;
    public $accHtml;
    
    public function __construct($arrLabels, $arrValues) {
        $this->arrLabels = $arrLabels;
        $this->arrValues = $arrValues;
        $this->arrOrderOpts = array();
        $this->hasSortOnSave = 0;
        $this->strListClass = "";
        $this->accHtml = "";        
    }
    
    /**
     * Wraps array of fields and values in a html table
     * @return string Returns html list table 
     */
    public function presentlist() {
        $this->accHtml = "<div class=\"table-responsive\">\n";
        $this->accHtml .= ($this->hasSortOnSave) ? 
                                    "<form method=\"post\">\n" . 
                                    "<input type=\"hidden\" name=\"mode\" value=\"savesort\">\n" : "";
        $this->accHtml .= "<table class=\"table-striped table-hover table-list ".$this->strListClass." \">\n"
                            . "   <tr>\n";        
        
        /* Loop column array and set table headers */
        foreach($this->arrLabels as $value) {
            if(empty($value)) continue;
            $this->accHtml .= "<th><span class=\"pull-left\">" . $value . ":</span></th>\n";
        }
        $this->accHtml .= "</tr>\n";
        /* >> End table headers */
        
        /* Loop data row array */
        foreach($this->arrValues as $row) {
            $this->accHtml .= "<tr>\n";
            /* Loop column array and get the matching key value from data rows */
            foreach($this->arrLabels as $key => $value) {
                $value = (getAbbr($key) === "da") ? date("d M Y H:i",$row[$key]) : $row[$key];
                
                if(($this->hasSortOnSave) && ($key === "iSortNum")) {
                    $value = "\n\t<select name=\"sortId[".reset($row)."]\">\n";
                    for($i = 1; $i <= count($this->arrValues);$i++) {
                        $selected = ($i === $row[$key]) ? "selected" : "";
                        $value .= "\t\t<option value=\"".$i."\" " . $selected . ">".$i."</option>\n";
                    }
                    $value .= "\t</select>\n";
                }
                
                $nowrap = ($key === "opts") ? " style=\"white-space: nowrap\"" : "";
                $this->accHtml .= "<td " . $nowrap . ">" . $value . "</td>\n";
            }
            
            $this->accHtml .= "</tr>\n";
        }
        /* >> End datarows */
        
        $this->accHtml .= "</table>\n</div>\n";
        $this->accHtml .= ($this->hasSortOnSave) ? getButton("submit", "Gem sortering", "", "btn-default pull-right") . "</form>\n" : "";
        return $this->accHtml;        
    }
    
    /**
     * Detail presenter list all information on a single element
     * Typically used in READ or DETAILS mode
     * @return string Returns accumulated html with details list
     */
    public function presentdetails() {
        $this->accHtml = "<div class=\"table-responsive\">\n" 
                            . "<table class=\"table-striped table-details ".$this->strListClass." \">\n";
        foreach($this->arrValues as $key => $value) {
            if(isset($this->arrLabels[$key]) && $this->arrLabels[$key]["Label"]) {
                $this->accHtml .= "</tr>\n";
                $this->accHtml .= "   <td><b>" . $this->arrLabels[$key]["Label"] . ":</b></td>\n";
                $this->accHtml .= "   <td>" . $value .  "   </td>\n";
                $this->accHtml .= "</tr>\n";
            }
        }

        $this->accHtml .= "</table>\n";
        return $this->accHtml;        
    }
}

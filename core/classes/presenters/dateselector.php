<?php
/**
 * Description of Date Selector Class
 * Class for making dropdown boxes for date selection
 * Outputs a selectbox for a defines format (day, month, year, hours, minutes)
 * Call example:
 * $dateObj = new DateSelector($timestamp);
 * $dateObj->dateselector([format], [name]);
 * 
 * @property int $stamp A timestamp value
 * @property string $strName A name for the selectbox (Ex: daStart, daStop, daBirthday ...)
 * @property string $strFormat The desired date format (Ex: day, month, year, hours, minutes)
 * @property string $accHtml String output with accumulated html
 * @property bool $useLocalNames Bool value for using local danish week and day names (Default: TRUE)
 * @property int $minuteIntVal Minute Interval Value. Can be set to 1, 5, 15 etc. (Default: 1)
 * @property array $arrTerms Single array with dateformat value, min and max value for the given format
 * @property array $arrDay2Local Array with local daynames (Index starts at 1)
 * @property array $arrMonth2Local Array with local monthnames (Index starts at 1)
 * 
 * @author Heinz K, Tech College, Dec 2016
 * */
class DateSelector {
    public $stamp;
    public $strName;
    public $strFormat;
    public $accHtml;
    public $useLocalNames;
    public $minuteIntVal;
    private $arrTerms;
    
    public $arrDay2Local = array(1 => "Søndag", "Mandag", "Tirsdag", 
                                        "Onsdag", "Torsdag", "Fredag", "Lørdag");
    
    public $arrMonth2Local = array(1 => "Januar", "Februar", "Marts", "April", "Maj", 
                                        "Juni", "Juli", "August", "September", "Oktober", 
                                        "November", "December");

    /**
     * 
     * @param int $stamp Uses a timestamp value as argument.
     */
    public function __construct($stamp) {
        $this->stamp = $stamp;
        $this->strName = "";
        $this->strFormat = "";
        $this->useLocalNames = TRUE;
        $this->minuteIntVal = 1;
        $this->accHtml = "";
    }
    
    /**
     * Method dateSelect
     * Initializes the format and builds select and box options
     * @param string $strFormat
     * @param string $strName
     * @return string $accHtml Returns an accumulated html string with select box and options
     */
    public function dateSelect($strFormat, $strName) {
        $this->strName = $strName;
        $this->strFormat = $strFormat;
        /* Initializes $this->arrTerms */
        $this->initTerms();
        /* Sets the selectbox html */
        $this->accHtml = "<select class=\"form-control input-sm dateselect\" name=\"".$this->strName . "_" . $this->strFormat ."\">\n";
        /* If set define the minute interval */
        $freq = ($this->strFormat === "minutes") ? $this->minuteIntVal : 1;
        /* Iterates the numFloor and numCeil and builds the select option bundle */
        for($i = $this->arrTerms["numFloor"]; $i <= $this->arrTerms["numCeil"];$i+= $freq) {
            /* Get the option text */
            $strOptText = $this->getOptText($i);
            /* Pad the value with a left zero to fit a match */
            $strSelected = (str_pad($i,2,0,STR_PAD_LEFT) === $this->arrTerms["numSelected"]) ? "selected" : "";
            /* Add option tag to accHtml var */
            $this->accHtml .= "<option value=\"" . $i . "\" " . $strSelected . ">" . $strOptText . "</option>\n";
        }
        $this->accHtml .= "</select>\n";
        return $this->accHtml;
    }
    
    /**
     * Method getOptsText (Get Option Text)
     * Outputs a user friendly option text for the given format
     * @param var $val The numeric value of a month, hour or minute (Ex: 1, 2, 3 etc... )
     * @return var $val Returns the processed output
     */
    private function getOptText($val) {
        switch(strtoupper($this->strFormat)) {
            case "MONTH":
                if($this->useLocalNames) {
                    $val = $this->arrMonth2Local[$val];
                } else {
                    $val = date("F", $this->stamp);
                }
                break;
            case "HOURS":
            case "MINUTES":
                    $val = str_pad($val,2,0,STR_PAD_LEFT);
                break;
        }
        return $val;
    }

    /**
     * Method initTerms (Initialize terms)
     * Creates an array ($this->arrTerms) with the following:
     * numSelected => the selected date according to the given format (Ex: 5, 12, 2016, 03, 06)
     * numFloor => The minimum number in the format range
     * numCeil => The maximum number in the format range
     * Has no return as this method set the class member propery $this->arrTerms
     */
    private function initTerms() {
        switch(strtoupper($this->strFormat)) {
            case "DAY":
                $this->arrTerms = array(
                        "numSelected" => date("d", $this->stamp),
                        "numFloor" => 1,
                        "numCeil" => 31
                    );
                break;
            case "MONTH":
                $this->arrTerms = array(
                        "numSelected" => date("m", $this->stamp),
                        "numFloor" => 1,
                        "numCeil" => 12
                    );
                break;
            case "YEAR":
                $this->arrTerms = array(
                        "numSelected" => date("Y", $this->stamp),
                        "numFloor" => date("Y", $this->stamp)-100,
                        "numCeil" => date("Y", $this->stamp)+20
                    );
                break;
            case "HOURS":
                $this->arrTerms = array(
                        "numSelected" => date("H", $this->stamp),
                        "numFloor" => "00",
                        "numCeil" => 23
                    );
                break;
            case "MINUTES":
                $this->arrTerms = array(
                        "numSelected" => date("i", $this->stamp),
                        "numFloor" => "00",
                        "numCeil" => 59
                    );
                break;
        }
    }
}


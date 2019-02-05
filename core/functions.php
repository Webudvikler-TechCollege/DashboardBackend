<?php
/**
 * Includes CMS header html
 */
function sysHeader() {
    global $db;
    require_once DOCROOT . "/cms/assets/incl/header.php";
}

/**
 * Includes CMS footer html
 */
function sysFooter() {
    global $db;
    require_once DOCROOT . "/cms/assets/incl/footer.php";
}

/**
 * Displays a read friendly var_dump
 * @param array $array
 * @param int $view
 */
function showme($array, $view = 0) {
    print ($view > 0) ? "<xmp>\n" : "<pre>\n";
    var_dump($array);
    print ($view > 0) ? "\n</xmp>" : "\n</pre>";
}

/**
 * Gets page mode from GET or POST - otherwise return default
 * @param string $default
 * @return string Returns selected string
 */
function setMode($default = "list") {
    $mode = filter_input(INPUT_POST, "mode", FILTER_SANITIZE_STRING);
    if(empty($mode)) { $mode = filter_input(INPUT_GET, "mode", FILTER_SANITIZE_STRING); }
    if(empty($mode)) { $mode = $default; }
    return $mode;
}

/**
 * Writes an icon with optional settings
 * @param string $strUrl URL for the icon reference
 * @param string $strIcon Icon name according to font awesome archive. The 'fa-' is prefixed... 
 * @param string $strTitle Content of title tag for the icon
 * @param string $strScript Optional Click Event - disables the URL
 * @param string $strClass Adds an additional class to the span tag
 * @return string Returns a string with accumulated html
 */
function getIcon($strUrl, $strIcon, $strTitle = "", $strScript = "", $strClass = "") {
    $attrClass = (!empty($strClass)) ? $strClass : "icon";
    $attrEvent = (!empty($strScript)) ? "onclick=\"" . $strScript . "\"" : "";
    $attrHref = (!empty($strUrl) && empty($strScript)) ? $strUrl : "Javascript:void(0)";

    $strHtml = "<a href=\"".$attrHref."\" ".$attrEvent.">";
    $strHtml .= "<span class=\"fa fa-" . $strIcon . " " . $attrClass . "\" title=\"" . $strTitle . "\"></span>\n";
    if(!empty($strUrl) || !empty($strScript)) {
        $strHtml .= "</a>\n";
    }
    return $strHtml;
}

/**
 * Builds a select box with name, options and selected value
 * Optional settings for multiple and event features
 * @param string $strName Name/Id attribute on selectbox
 * @param array $arrOptions Multi-dimensional array of options 
 *          (key = numeric, value = array(option-value, option-text))
 * @param var $value Value to be selected (Optional) 
 * @param bool $isMultiple Set to true if multple select
 * @param string $strOnChangeEvent Set onchange event call if needed (Ex: doSomething())
 * @return string Returns a html string with a select box
 */
function SelectBox($strName, $arrOptions, $value = NULL, $isMultiple = FALSE, $strOnChangeEvent = '') {
    $strAttrName = ($isMultiple === TRUE) ? $strName . "[]" : $strName; 
    $strMultiple = ($isMultiple === TRUE) ? "multiple" : "";
    $strEvent = (!empty($strOnChangeEvent)) ? "onchange=\"".$strOnChangeEvent."\"" : "";
    
    $strHtml = "<select class=\"form-control\" id=\"" . $strName . "\" " . 
                    "name=\"" . $strAttrName . "\" " . $strMultiple . " " . $strEvent . ">\n";
    
    foreach($arrOptions as $arrOptionInfo) {
        $arrOptionInfo = array_values($arrOptionInfo);
        $selected = ($isMultiple === TRUE) ? 
                        in_array($arrOptionInfo[0], $value) ? "selected" : "" :
                        ($value === $arrOptionInfo[0]) ? "selected" : "";
        
        $padding = isset($arrOptionInfo[2]) ? $arrOptionInfo[2] : "";
        
        $strHtml .= "<option value=\"" . $arrOptionInfo[0] . "\" " . $selected . ">" . $arrOptionInfo[1] . "</option>\n";
    }
    $strHtml .= "</select>\n";
    return $strHtml;
}

/**
 * @param string $type
 * @param string $value
 * @param string $event
 * @param string $class
 * @param string $data
 * @return string
 */
function getButton($type, $value, $event = "", $class = "btn-default", $data = "") {
    $event = !empty($event) ? " onclick=\"" . $event . "\"" : "";
    $data = !empty($data) ? $data : "";
    $strHtml = "<button type=\"" . $type . "\" class=\"btn " . $class . "\" " . $event . " " . $data . ">" . $value . "</button>\n";
    return $strHtml;
}

/**
 * Creates a button for the topic relation picker
 * @param int $elmType - Module Type Integer
 * @param int $elmId - Element Id
 * @return string
 */
function getTopicPicker($elmType, $elmId) {
    $strHtml = "<button type=\"button\" class=\"btn btn-default btn-topics\" data-type=\"".$elmType."\" data-id=\"".$elmId."\">Vælg emner</button>\n";
    return $strHtml;
}

/**
 * Cleans a string for number and symbols
 * @param string $string
 * @return string Returns a letter contained string 
 */
function onlyLetters($string) {
    return preg_replace("/[^a-zA-Z]+$/", "", $string);
}

/**
 * Cleans a string for alphas and symbols
 * @param string $string
 * @return string Returns a letter contained string 
 */
function onlyNumbers($string) {
    return preg_replace("/[^0-9]/", "", $string);
}

/**
 * Get table field abbreviation
 * @param string $str
 * @return string Returns string abbreviation
 */
function getAbbr($str) {
    $abbr = preg_split('/(?=[A-Z])/',$str);
    return reset($abbr);
}

/**
 * Minifies file contents (CSS/JS)
 * @param string $input
 * @return string Returns minified string
 */
function __minify_x($input) {
    return str_replace(array("\n", "\t", " ", "\r\n", "\r", "     ", "  "), " ", $input);
}

/**
 * 
 * @param type $url
 * @return type
 */
function getWebsafe($str) {
    $arrChars = array("\'" => "''","Æ" => "ae","æ" => "ae","Ø" => "oe",
                        "ø" => "oe","Å" => "aa","å" => "aa");
    $webstr = strtr(trim(strtolower($str)), $arrChars);
    $webstr = preg_replace('/[^a-z0-9]+/', "-", $webstr);
    return $webstr;
}

/*
 * Set Default Value for filter_inputs
 */
function getDefaultValue($val) {
    $options = array('options' => array('default' => $val));
    return $options;
}

/*
 * Returns a date/time string with locale month and day names
 * @param int $iStamp A timestamp
 * @param bool $iUseHours Use hours and minutes if set to true
 * @return string Returns a formatted date string 
 */
function date2local($iStamp, $iUseHours = 0) {
    $arrDays = array(1 => "Mandag","Tirsdag","Onsdag","Torsdag","Fredag","Lørdag","Søndag");
    $arrMonths = array(1 => "Januar","Februar","Marts","April","Maj","Juni","Juli","August","September","Oktober","November","December");
    $dateFormat = date("j", $iStamp) . ". " . $arrMonths[date("n",$iStamp)] . " " . date("Y", $iStamp);
    if($iUseHours) {
        $dateFormat .= " " . date("k\l. H:i", $iStamp);
    }
    return $dateFormat;
}

/*
 * Returns a friendly date & time format according to locale settings 
 * @param int $iStamp
 * @return string Returns a formatted date string 
 */
function time2local($iStamp) {
    return date2local($iStamp,1);
}

/**
 * Creates a timestamp from form date select values
 * @param string $strElm Name of the date field
 * @return int Returns a timestamp
 */
function makeStamp($strElm) {
    $arrFormats = array("day", "month", "year", "hours", "minutes");
    $arrDate = array();

    foreach($arrFormats as $value) {
        $arrDate[$value] = filter_input(INPUT_POST, $strElm . "_" . $value, FILTER_SANITIZE_NUMBER_INT, getDefaultValue(0));
    }
    return mktime($arrDate["hours"],$arrDate["minutes"],0,$arrDate["month"],$arrDate["day"],$arrDate["year"]);
}

/**
 * Converts a bool value to FA check/ban icon
 * @param bool $val
 * @return string Returns span tag with fa icon  
 */
function boolToIcon($val) {
    $strIcon = ($val > 0) ? "check" : "ban";
    return "<span class=\"testing fa fa-" . $strIcon . "\"></span>\n";
}

/**
 * Converts a bool value to string
 * @param int $val
 * @return string Returns true/false as a string value
 */
function boolToText($val) {
    return ($val > 0) ? "Ja" : "Nej";
}

/**
 * Converts a bool value to a custom string
 * @param int $value
 * @param array Array with false/true option
 * @return string Returns true/false as a string value
 */
function boolToCustom($val, $array) {
    return $array[$val];
}

/**
 * Formats a price to local 
 * @param float $val
 * @return float Formatted price
 */
function formatPrice($val, $friendly = 1, $calcFloat = 1) {
	if($calcFloat > 0) {
		$val = $val/100;
	}
    if($friendly > 0) {
        return number_format($val,2,",","."); 
    } else {
        return number_format($val,2,"."); 
    }
}
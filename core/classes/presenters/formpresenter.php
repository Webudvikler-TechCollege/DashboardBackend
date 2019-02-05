<?php
/**
 * Description of Form Presenter
 * @author Heinz K, Tech College, Nov 2016
 * 
 * @property array $arrColumns Multiple array (key = db fieldname, value = friendly name)
 * @property array $arrElements Multiple array (key = db fieldname, value = array(input_type, isrequired, ismultiple))
 * @property array $arrValues Single array (key = db fieldname, value = db row values)
 * @property string $formId Form id - default = 'adminform'
 * @property string $formMethod Form method attribute - default = 'POST'
 * @property string $formAction Form action attribute - default = 'save'
 * @property string $formClass Form class attribute - default = NULL
 * @property string $accHtml Accumulated html with form output
 * @property bool $iUseEnctype Bool to set enctype attribute. Required for file upload.
 */
class FormPresenter {
    public $arrColumns;
    public $arrValues;
    public $arrButtons;
    public $accHtml;    
    public $formId;
    public $formMethod;
    public $formAction;
    public $formClass;
    public $formEvalName;
    public $iUseEnctype;

    public function __construct($arrColumns, $arrValues) {
        $this->arrColumns = $arrColumns;
        $this->arrValues = $arrValues;
        $this->arrButtons = array();
        $this->formId = "adminform";
        $this->formMethod = "POST";
        $this->formAction = "save";
        $this->formEvalName = "eval";
        $this->formClass = "";
        $this->accHtml = "";
    }    
        
    /**
     * Form Presenter Method
     * Iterates arrElements and runs a switch on inner arrays first element.
     * arrElements, arrColumns and arrValues runs on the same key reference
     * Accumulates strHtml and returns string
     * @return type
     */
    public function presentform() {
        $strEnctype = ($this->iUseEnctype === TRUE) ? "enctype=\"multipart/form-data\"" : "";

        $arrIsRequired = array();
        
        $this->accHtml = "\n\n<!-- FORM BEGIN -->\n" .
                            "<form method=\"".$this->formMethod."\" id=\"".$this->formId."\" class=\"form-horizontal " . $this->formClass . "\" " . $strEnctype . ">\n"
                            . " <fieldset>\n"
                            . "  <input type=\"hidden\" name=\"mode\" value=\"".$this->formAction."\">\n";

        foreach($this->arrColumns as $key => $values) {

	        $strIsRequired = (isset($values["Required"]) && $values["Required"] > 0) ? "required" : "";
            
            if(!empty($strIsRequired)) {
                $arrIsRequired[$key] = array($values["Label"],$values["Formtype"]);
            }

            if(isset($values["Formtype"])) {
                switch($values["Formtype"]) {

                    case crud::INPUT_HIDDEN: 
                        $this->accHtml .= "<input type=\"hidden\" name=\"".$key."\" id=\"".$key."\"  value=\"" . $this->arrValues[$key] . "\">\n";
                        break;

                    case crud::INPUT_TEXT:
                    case crud::INPUT_TEXT_READONLY:
                    case crud::INPUT_EMAIL:                    
                        $strReadOnly = ($values["Formtype"] === crud::INPUT_TEXT_READONLY) ? "readonly" : "";

                        $this->accHtml .= "<div class=\"form-group\" data-group=\"".$key."\">\n";
                        $this->accHtml .= "   <label class=\"col-sm-3 control-label ".$strIsRequired."\" for=\"".$key."\">" . $values["Label"] . ":</label>\n";
                        $this->accHtml .= "   <div class=\"col-sm-9\">\n";
                        $this->accHtml .= "      <input " . $strReadOnly . " type=\"".$values["Formtype"]."\" " . $strIsRequired . " name=\"".$key."\" id=\"".$key."\" placeholder=\"Indtast " . $values["Label"] . "\" value=\"".$this->arrValues[$key]."\" class=\"form-control\">\n";
                        $this->accHtml .= "   </div>\n";
                        $this->accHtml .= "</div>\n";
                        break;                    

                    case crud::INPUT_PASSWORD:
                        if (!empty($this->arrValues[$key])) { unset($arrIsRequired['vcPassword']); }
                        $strValue = ($values["Formtype"] === "password") ? "" : $this->arrValues[$key];

                        $this->accHtml .= "<div class=\"form-group\" data-group=\"".$key."\">\n";
                        $this->accHtml .= "   <label class=\"col-sm-3 control-label ".$strIsRequired."\" for=\"".$key."\">" . $values["Label"] . ":</label>\n";
                        $this->accHtml .= "   <div class=\"col-sm-9\">\n";
                        $this->accHtml .= "      <input  type=\"".$values["Formtype"]."\" name=\"".$key."\" id=\"".$key."\" placeholder=\"Indtast " . $values["Label"] . "\" value=\"".$strValue."\" class=\"form-control\" " . 
                                                    "readonly onfocus=\"this.removeAttribute('readonly');\" data-original-title=\"Klik for at indtaste nyt password\" data-toggle=\"tooltip\" data-placement=\"bottom\">\n";
                        $this->accHtml .= "   </div>\n";
                        $this->accHtml .= "</div>\n";
                        break;                    

                    case crud::INPUT_TEXTAREA:
                        $this->accHtml .= "<div class=\"form-group\" data-group=\"".$key."\">\n";
                        $this->accHtml .= "   <label class=\"col-sm-3 control-label ".$strIsRequired."\" for=\"".$key."\">" . $values["Label"] . ":</label>\n";
                        $this->accHtml .= "   <div class=\"col-sm-9\">\n";
                        $this->accHtml .= "      <textarea class=\"form-control\" name=\"".$key."\" id=\"".$key."\">" .$this->arrValues[$key]."</textarea>\n";
                        $this->accHtml .= "   </div>\n";
                        $this->accHtml .= "</div>\n";
                        break;

                    case crud::INPUT_TEXTEDITOR:
                        $this->accHtml .= "<div class=\"form-group\" data-group=\"".$key."\">\n";
                        $this->accHtml .= "   <label class=\"col-sm-3 control-label ".$strIsRequired."\" for=\"".$key."\">" . $values["Label"] . ":</label>\n";
                        $this->accHtml .= "   <div class=\"col-sm-9\">\n";
                        $this->accHtml .= "      <textarea class=\"texteditor form-control\" name=\"".$key."\" id=\"".$key."\">" . $this->arrValues[$key] . "</textarea>\n";
                        $this->accHtml .= "   </div>\n";
                        $this->accHtml .= "</div>\n";
                        break;

                    case crud::INPUT_SELECT:
                        $this->accHtml .= "<div class=\"form-group\" data-group=\"".$key."\">\n";
                        $this->accHtml .= "   <label class=\"col-sm-3 control-label ".$strIsRequired."\" for=\"".$key."\">" . $values["Label"] . ":</label>\n";
                        $this->accHtml .= "   <div class=\"col-sm-9\">\n";
                        $this->accHtml .= "      ".$this->arrValues[$key]."\n";
                        $this->accHtml .= "   </div>\n";
                        $this->accHtml .= "</div>\n";                    
                        break;

                    case crud::INPUT_CHECKBOX:
                        $strChecked = ($this->arrValues[$key]) ? " checked " : "";

                        $this->accHtml .= "<div class=\"form-group\">\n";
                        $this->accHtml .= "   <label class=\"col-sm-3 control-label\" for=\"".$key."\">".$values["Label"]."</label>\n";
                        $this->accHtml .= "   <div class=\"col-sm-9\">\n";
                        $this->accHtml .= "      <div class=\"checkbox\">\n";
                        $this->accHtml .= "         <label>\n";
                        $this->accHtml .= "            <input type=\"checkbox\" ".$strChecked." name=\"".$key."\" " . 
                                                            "id=\"".$key."\" value=\"1\">";
                        $this->accHtml .= "         </label>\n";
                        $this->accHtml .= "      </div>\n";
                        $this->accHtml .= "   </div>\n";
                        $this->accHtml .= "</div>\n";
                        break;
                    
                    case crud::INPUT_CHECKBOXMULTI:   
                        $this->accHtml .= "<div class=\"form-group\">\n";
                        $this->accHtml .= "   <label class=\"col-sm-3 control-label\" for=\"".$key."\">" . $values["Label"] . ":</label>\n";
                        $this->accHtml .= "   <div class=\"col-sm-9\">\n";
                        $this->accHtml .= "      <div class=\"checkbox\">\n";
                
                        /**
                         * $optValues:
                         * 0 => int iGroupID
                         * 1 => string vcTitle
                         * 2 => string spaces
                         * 3 => bool Selected Status
                         */
                        
                        foreach($this->arrValues[$key] as $k => $optValues) {
                            $strTitle = empty($optValues[2]) ? "<b>" . $optValues[1] . "</b>" : $optValues[1];
                            $strChecked = ($optValues[3] > 0) ? "checked" : "";
                            $this->accHtml .= "         <label>\n";
                            $this->accHtml .= $optValues[2] . "<input type=\"checkbox\" ".$strChecked." name=\"".$key."[]\" " . 
                                                "id=\"".$optValues[0]."\" value=\"".$optValues[0]."\"></input>".$strTitle."\n";
                            $this->accHtml .= "         </label><br />\n";
                        }
                        $this->accHtml .= "      </div>\n";
                        $this->accHtml .= "   </div>\n";
                        $this->accHtml .= "</div>\n";
                    break;    
            
                    CASE crud::INPUT_RADIO:
                        $this->accHtml .= "<div class=\"form-group\">\n";
                        $this->accHtml .= "   <label class=\"col-sm-3 control-label\"></label>\n";
                        $this->accHtml .= "   <div class=\"col-sm-9\">\n";
                        $this->accHtml .= "      <div class=\"checkbox\">\n";
                        $this->accHtml .= "         <label>\n";
                        $this->accHtml .= "            <input type=\"radio\" ".$strChecked." name=\"".$key."\" id=\"".$key."\" value=\"1\">" . $values["Label"] . "\n";
                        $this->accHtml .= "         </label>\n";
                        $this->accHtml .= "      </div>\n";
                        $this->accHtml .= "   </div>\n";
                        $this->accHtml .= "</div>\n";
                        break;

                    case crud::INPUT_DATE:
                        $stamp = ($this->arrValues[$key] > 0) ? $this->arrValues[$key] : time();
                        $d = new DateSelector($stamp);
                        $this->accHtml .= "<div class=\"form-group\" data-group=\"".$key."\">\n";
                        $this->accHtml .= "   <label class=\"col-sm-3 control-label ".$strIsRequired."\" for=\"".$key."\">" . $values["Label"] . ":</label>\n";
                        $this->accHtml .= "   <div class=\"col-sm-9 form-inline\">\n";
                        $this->accHtml .= "   " . $d->dateselect("day",$key);
                        $this->accHtml .= "   " . $d->dateselect("month",$key);
                        $this->accHtml .= "   " . $d->dateselect("year",$key);
                        $this->accHtml .= "   </div>\n";
                        $this->accHtml .= "</div>\n";
                    break;

                    case crud::INPUT_DATETIME:
                        $stamp = ($this->arrValues[$key] > 0) ? $this->arrValues[$key] : time();
                        $d = new DateSelector($stamp);
                        $this->accHtml .= "<div class=\"form-group\" data-group=\"".$key."\">\n";
                        $this->accHtml .= "   <label class=\"col-sm-3 control-label ".$strIsRequired."\" for=\"".$key."\">" . $values["Label"] . ":</label>\n";
                        $this->accHtml .= "   <div class=\"col-sm-9 form-inline\">\n";
                        $this->accHtml .= "   " . $d->dateselect("day",$key);
                        $this->accHtml .= "   " . $d->dateselect("month",$key);
                        $this->accHtml .= "   " . $d->dateselect("year",$key);
                        $this->accHtml .= "   " . $d->dateselect("hours",$key);
                        $this->accHtml .= "   " . $d->dateselect("minutes",$key);
                        $this->accHtml .= "   </div>\n";
                        $this->accHtml .= "</div>\n";
                    break;

                    case crud::INPUT_FILE:
                        $this->accHtml .= "<div class=\"form-group\" data-group=\"".$key."\">\n";
                        $this->accHtml .= "   <label class=\"col-sm-3 control-label ".$strIsRequired."\" for=\"".$key."\">" . $values["Label"] . ":</label>\n";
                        $this->accHtml .= "   <div class=\"col-sm-9\">\n";
                        $this->accHtml .= "       <div class=\"fileeditor form-control\">\n";
                        $this->accHtml .= "           <input type=\"file\" name=\"".$key."\" value=\"".$this->arrValues[$key]."\" />\n";
                        $this->accHtml .= "           <div class=\"filename\" name=\"holder_".$key."\">".$this->arrValues[$key]."</div>\n";
                        $this->accHtml .= "       </div>\n";
                        $this->accHtml .= "   </div>\n";
//                        $this->accHtml .= "   <div class=\"col-sm-9\">\n";
//                        $this->accHtml .= "      <input type=\"file\" name=\"".$key."\" id=\"".$key."\" value=\"".$this->arrValues[$key]."\" class=\"form-control\">\n";
//                        $this->accHtml .= "   </div>\n";
                        $this->accHtml .= "</div>\n";
                        break;     

                    case crud::INPUT_IMAGEEDITOR:
                    case crud::INPUT_FILEEDITOR:
                        $root = ($values["Formtype"] === "fileeditor") ? "/documents/" : "/images/";
                        $this->accHtml .= "<div class=\"form-group\" data-group=\"".$key."\">\n";
                        $this->accHtml .= "   <label class=\"col-sm-3 control-label ".$strIsRequired."\">" . $values["Label"] . ":</label>\n";
                        $this->accHtml .= "   <div class=\"col-sm-9\">\n";
                        $this->accHtml .= "       <div class=\"fileeditor form-control\">\n";
                        $this->accHtml .= "           <input type=\"hidden\" name=\"".$key."\" value=\"".$this->arrValues[$key]."\" />\n";
                        $this->accHtml .= "           <div class=\"filename\" name=\"holder_".$key."\">".$this->arrValues[$key]."</div>\n";
                        $this->accHtml .= "           <button type=\"button\" class=\"btn btn-xs btn-primary btn-file\" id=\"".$key."\" data-root=\"".$root."\">Vælg fil</button>\n";
                        $this->accHtml .= "       </div>\n";
                        $this->accHtml .= "   </div>\n";
                        $this->accHtml .= "</div>\n";
                    break;

                    case crud::INPUT_CODEEDITOR:
                        $this->accHtml .= "<link rel=\"stylesheet\" href=\"/cms/assets/js/codemirror/lib/codemirror.css\" />\n";
                        $this->accHtml .= "<script src=\"/cms/assets/js/codemirror/lib/codemirror.js\"></script>\n";
                        $this->accHtml .= "<script src=\"/cms/assets/js/codemirror/addon/edit/matchbrackets.js\"></script>\n";
                        $this->accHtml .= "<script src=\"/cms/assets/js/codemirror/mode/htmlmixed/htmlmixed.js\"></script>\n";
                        $this->accHtml .= "<script src=\"/cms/assets/js/codemirror/mode/xml/xml.js\"></script>\n";
                        $this->accHtml .= "<script src=\"/cms/assets/js/codemirror/mode/javascript/javascript.js\"></script>\n";
                        $this->accHtml .= "<script src=\"/cms/assets/js/codemirror/mode/css/css.js\"></script>\n";
                        $this->accHtml .= "<script src=\"/cms/assets/js/codemirror/mode/clike/clike.js\"></script>\n";
                        $this->accHtml .= "<script src=\"/cms/assets/js/codemirror/mode/php/php.js\"></script>\n";

                        $this->accHtml .= "<div class=\"form-group\" data-group=\"".$key."\">\n";
                        $this->accHtml .= "   <label class=\"col-sm-12 control-label ".$strIsRequired."\" for=\"".$key."\">" . $values["Label"] . ":</label>\n";
                        $this->accHtml .= "   <div class=\"col-sm-12\">\n";
                        $this->accHtml .= "      <textarea class=\"form-control code\" name=\"".$key."\" id=\"".$key."\">".$this->arrValues[$key]."</textarea>\n";
                        $this->accHtml .= "   </div>\n";
                        $this->accHtml .= "</div>\n";

                        $this->accHtml .= "<script>\n";
                        $this->accHtml .= "$(function() {\n";
                        $this->accHtml .= "  var editor = CodeMirror.fromTextArea(document.getElementById('".$key."'), {\n";
                        $this->accHtml .= "    lineNumbers: true,\n";
                        $this->accHtml .= "    matchBrackets: true,\n";
                        switch($this->arrValues["Codemode"]) {
                            default:
                            case "MAIN":
                                $this->accHtml .= "    mode: \"application/x-httpd-php\",\n";
                                break;
                            case "CSS":
                                $this->accHtml .= "    mode: \"text/css\",\n";
                                break;
                            case "JS":
                                $this->accHtml .= "    mode: \"text/javascript\",\n";
                                break;
                        }
                        $this->accHtml .= "    indentUnit: 4,\n";
                        $this->accHtml .= "    indentWithTabs: true,\n";
                        $this->accHtml .= "    enterMode: \"keep\",\n";
                        $this->accHtml .= "    tabMode: \"shift\"\n";
                        $this->accHtml .= "  })\n";
                        $this->accHtml .= "  editor.getValue();\n";
                        $this->accHtml .= "});\n";
                        $this->accHtml .= "</script>\n";
                    break;            
                }
            }
        }
        
        /*
        $val = new Validate($this->formEvalName);
        $val->strFormId = $this->formId;
        $val->arrFields = $arrIsRequired;
        $this->accHtml .= $val->setValidate();
         * 
         */
        
        $this->accHtml .= "\n\t<div class=\"buttonpanel\">\n\t";
        if(empty($this->arrButtons)) {
            $this->accHtml .= getButton("button","Annuller","goback()") ."\t";
            $this->accHtml .= getButton("submit","Gem");
        } else {
            foreach($this->arrButtons as $key => $value) {
                $this->accHtml .= $value;
            }
        }
        $this->accHtml .= "</div>\n";
        
        $this->accHtml .= " </fieldset>\n</form>\n<!-- FORM END -->\n\n";   
        
        return $this->accHtml;
        
    }
}
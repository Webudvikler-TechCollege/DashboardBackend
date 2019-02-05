<?php
/**
 * Presenter for admin text & html snippets
 * @copyright (c) 2016, Heinz K, Tech College
 */
class textPresenter {
    
    /**
     * Panel presenter for Module & Mode titles and navigation buttons
     * @param string $strModuleName Module Name eg. Songs, Artist etc...
     * @param string $strModuleMode Name on the CRUD mode
     * @param array $arrButtonPanel Array of navigation buttons
     */
    static function presentpanel($strModuleName, $strModuleMode,$arrButtonPanel = array()) {
        $accHtml = "<div class=\"mainheader\">\n";
        $accHtml .= "   <div class=\"pull-left\">\n";
        $accHtml .= "       <h1>".$strModuleName."</h1>\n";
        $accHtml .= "       <h2>".$strModuleMode."</h2>\n";
        $accHtml .= "   </div>\n";

        if(isset($arrButtonPanel) && count($arrButtonPanel) > 0) {
            $accHtml .= "<div class=\"buttonpanel\">\n";
            foreach($arrButtonPanel as $key => $value) {
                $accHtml .= $value;
            }
            $accHtml .= "</div>\n";
        }
        $accHtml .= "</div>\n";
        return $accHtml;        
    }
    
    static function presenttext($strContent) {
        $accHtml = "<div class=\"maintext\">" . $strContent . "</div>";
        return $accHtml;
    }
}
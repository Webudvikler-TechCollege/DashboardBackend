<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$strJs = '';
$strJsFiles = isset($_GET["f"]) && !empty($_GET["f"]) ? $_GET["f"] : "";

if(!empty($strJsFiles)) {
    $arrJsFiles = explode(",",$strJsFiles);
    
    foreach ($arrJsFiles as $key => $filename) {
        $strJs .= file_get_contents(filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . '/cms/assets/js/' . $filename . '.js') . "\n\n";
    }
    header("Content-type: text/javascript");
    //echo "<pre>" . $strJs . "</pre>";
    echo $strJs;
}



?>

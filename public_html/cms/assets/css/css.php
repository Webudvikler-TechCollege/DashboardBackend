<?php
$strCss = '';
$strCssFiles = isset($_GET["f"]) && !empty($_GET["f"]) ? $_GET["f"] : "";
//$strCssPath = isset($_GET["p"]) && !empty($_GET["p"]) ? $_GET["p"] : "";

if(!empty($strCssFiles)) {
    $arrCssFiles = explode(",",$strCssFiles);
    
    foreach ($arrCssFiles as $key => $filename) {
        $strCss .= file_get_contents(filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/cms/assets/css/" . $filename . '.css') . "\n\n";
    }
    header("Content-type: text/css");
    echo $strCss;
}
?>

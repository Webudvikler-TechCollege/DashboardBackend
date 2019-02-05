<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$root = filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING);
$dir = filter_input(INPUT_POST, "dir", FILTER_SANITIZE_STRING);
$postDir = rawurldecode($root.(!empty($dir) ? $dir : null));

if(file_exists($postDir)) {
    $folders = array_diff(scandir($postDir), array(".",".."));
    
    $strHtml = "<ul class=\"foldertree\">\n";
    
    foreach($folders as $key => $value) {
        if(is_dir($postDir . $value)) {
            $dirRel = $dir . $value;
            $strHtml .= "<li class=\"directory collapsed\"><a rel=\"" . $dirRel . "/\">" . $value . "</a></li>\n";
        }
    }
    
    $strHtml .= "</ul>\n";
        
    echo $strHtml;
}


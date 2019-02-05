<?php

class router {
    
    private $arrUrls = array();
    private $_method = array();
    
    public function __construct() {
        showme($this->arrUrls);
    }
    
    public function add($url, $iPageID = 0, $method = NULL) {
        if(strstr($url, ".htm")) {
            $this->arrUrls[] = array("/" . trim($url, "/"), $iPageID);
        } else {
            $this->arrUrls[] = array(trim($url),$iPageID);
        }
        
        if($method != NULL) {
            $this->_method[] = $method;
        }
        
    }
    
    public function submit() {
        
        $urlGetParam = isset($_GET["url"]) ? "/" . $_GET["url"] : "/";
        $result = 0;
        
        foreach ($this->arrUrls as $key => $value) {
            if(preg_match("#^$value[0]$#", $urlGetParam)) {
                $result = 1;
                return $value[1];
            }
        }
        
        if(!$result) {
            echo "404";
            exit();
        }
    }    
}

<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class dbconf extends db {
    function __construct() {
        $this->dbhost = "your.host";
	    $this->dbuser = "your.username";
        $this->dbpassword = "your.password";
        $this->dbname = "your.database";
        $db = parent::_connect();
    }
}
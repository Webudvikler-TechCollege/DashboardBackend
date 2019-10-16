<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class dbconf extends db {
    function __construct() {
        $this->dbhost = "sql.itcn.dk:3306";
	    $this->dbuser = "websiteuser.TCAA";
        $this->dbpassword = "d0WDX17su2";
        $this->dbname = "websiteuser2.TCAA";
        $db = parent::_connect();
    }
}
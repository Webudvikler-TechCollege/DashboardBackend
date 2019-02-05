<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class dbconf extends db {
    function __construct() {
        $this->dbhost = "sql.itcn.dk";
	    $this->dbuser = "heka.TCAA";
        $this->dbpassword = "8Y0q37KAca";
        $this->dbname = "heka2.TCAA";
        $db = parent::_connect();
    }
}
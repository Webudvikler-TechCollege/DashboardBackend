<?php
/* Global Tools */
date_default_timezone_set('Europe/Copenhagen');
define("DOCROOT", filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING));
define("COREPATH", substr(DOCROOT, 0, strrpos(DOCROOT,"/")) . "/core/");

require_once COREPATH . 'constants.php';
require_once COREPATH . 'functions.php';
require_once COREPATH . 'classes/autoload.php';

/* Classloader - loads class on call from /core/classes/ */
$classloader = new AutoLoad();
$db = new dbconf();

$auth = new Auth();
$auth->authenticate();

if(!isset($auth->user->admin)) {
    echo $auth->loginform(Auth::ERR_NOACCESS);
    exit();
}

/* Get config strings */
$cfgObj = new Config();
$arrConfig = $cfgObj->createArray();
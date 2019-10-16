<?php
require_once filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/assets/incl/init.php";

$arr_api = [];

$news = new news();
$arr_api["news"] = $news->getAll();

$mh_activity = new mh_activity();
$arr_api["activity"] = $mh_activity->getAll();

$mh_subject = new mh_subject();
$arr_api["subject"] = $mh_subject->getAll();

$medie = new Medie();
$arr_api["medie"] = $medie->getAll();

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
echo json_encode($arr_api);
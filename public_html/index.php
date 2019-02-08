<?php
require_once filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/assets/incl/init.php";

//Hent alle aktiviteter
$a = new mh_activity();
//Udskriv som json
echo json_encode($a->getAll());

//Hent enkelt aktivitet
$a->getItem(12);
//Udskriv som json
//echo json_encode($a->arrValues);

//Hent alle nyheder
$n = new news();
//Udskriv som json
//echo json_encode($n->getAll());

?>

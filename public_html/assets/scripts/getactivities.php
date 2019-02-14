<?php
require_once filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/assets/incl/init.php";

//Hent kommende aktiviteter
$a = new mh_activity();
//Udskriv som json
echo json_encode($a->getactual());
?>

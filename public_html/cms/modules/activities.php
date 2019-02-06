<?php
require_once filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/cms/assets/incl/init.php";
$mode = setMode();
$strModuleName = "Aktiviteter";

switch (strtoupper($mode)) {
    /* List Mode */
    case "LIST":
        $strModuleMode = "Oversigt";
        sysHeader();
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button", "Genindlæs data", "getUrl('?mode=getdata')");
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName, $strModuleMode, $arrButtonPanel);
        /* Fetch artists from DB */
        $obj = new mh_activity();
        $rows = $obj->getall();

        $arrLabels = array(
            "opts" => "Options",
            "vcSubject" => $obj->arrColumns["vcSubject"]["Label"],
            "vcClassroom" => $obj->arrColumns["vcClassroom"]["Label"],
            "vcClass" => $obj->arrColumns["vcClass"]["Label"],
            "daTime" => $obj->arrColumns["daTime"]["Label"]
        );

        /* Format rows with option icons */
        foreach ($rows as $key => $row) {
            $rows[$key]["opts"] = getIcon("?mode=details&iActivityID=" . $row["iActivityID"], "eye");
        }

        /* Call list presenter object  */
        $p = new listPresenter($arrLabels, $rows);
        echo $p->presentlist();

        sysFooter();
        break;

    case "DETAILS":
        $iActivityID = filter_input(INPUT_GET, "iActivityID", FILTER_SANITIZE_NUMBER_INT);

        $strModuleMode = "Detaljer";
        sysHeader();
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button", "Oversigt", "document.location.href='?mode=list'");

        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName, $strModuleMode, $arrButtonPanel);

        $obj = new mh_activity();
        $obj->getitem($iActivityID);

        $obj->arrValues["daTime"] = time2local($obj->arrValues["daTime"]);

        $p = new listPresenter($obj->arrColumns, $obj->arrValues);
        echo $p->presentdetails();

        sysFooter();
        break;

	case "GETDATA":
		$data = new mh_getdata();
		$data->run_update();
		header("Location: ?mode=list");
		break;
}

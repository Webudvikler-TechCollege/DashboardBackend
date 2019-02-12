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
            "vcEdu" => "Uddannelse",
            "daTime" => $obj->arrColumns["daTime"]["Label"]
        );

        /* Format rows with option icons */
        foreach ($rows as $key => $row) {

            $rows[$key]["opts"] = getIcon("?mode=details&iActivityID=" . $row["iActivityID"], "eye") . 
                                    getIcon("?mode=edit&iActivityID=" . $row["iActivityID"], "pencil");
            $rows[$key]["vcEdu"] = $obj->findTeam($rows[$key]["vcClass"]);
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

    case "EDIT";
        $iActivityID = filter_input(INPUT_GET, "iActivityID", FILTER_SANITIZE_NUMBER_INT);

        $obj = new mh_activity();
        if ($iActivityID > 0) {
            $obj->getitem($iActivityID);
        }

        $strModuleMode = ($iActivityID > 0) ? "Rediger" : "Opret nyt nyhed";
        sysHeader();

        /* Set array button panel */
        $arrButtonPanel = array();

        if ($iActivityID > 0) {
            $arrButtonPanel[] = getButton("button", "Detaljer", "getUrl('?mode=details&iActivityID=" . $iActivityID . "')");
        }
        $arrButtonPanel[] = getButton("button", "Oversigt", "getUrl('?mode=list')");
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName, $strModuleMode, $arrButtonPanel);

        //$obj->arrValues["daTime"] = $obj->arrValues["daTime"];

        /* Call From Presenter */
        $form = new formPresenter($obj->arrColumns, $obj->arrValues);
        echo $form->presentform();

        sysFooter();
        break;

    case "SAVE";
        $news = new news();
	    $news->arrColumns["daStart"]["Value"] = makeStamp("daStart");
        $iActivityID = $news->save();
        header("Location: ?mode=details&iActivityID=" . $iActivityID);
        break;

    case "DELETE":
        $obj = new news();
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
        $obj->delete($id);
        header("Location: ?mode=list");
        break;

    
    case "GETDATA":
		$data = new mh_getdata();
		$data->run_update();
		header("Location: ?mode=list");
		break;
}

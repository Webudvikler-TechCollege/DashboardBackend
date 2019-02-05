<?php
require_once filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/cms/assets/incl/init.php";
$mode = setMode();
$strModuleName = "Medie";

switch (strtoupper($mode)) {
    /* List Mode */
    case "LIST":
        $strModuleMode = "Oversigt";
        sysHeader();
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button", "Opret medie", "getUrl('?mode=edit&iMedieID=-1')");
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName, $strModuleMode, $arrButtonPanel);
        /* Fetch artists from DB */
        $obj = new medie();
        $rows = $obj->getall();

        $arrLabels = array(
            "opts" => "Options",
            "vcTitle" => $obj->arrColumns["vcTitle"]["Label"],
            "iIsActive" => $obj->arrColumns["iIsActive"]["Label"],
            "daCreated" => $obj->arrColumns["daCreated"]["Label"]
        );

        /* Format rows with option icons */
        foreach ($rows as $key => $row) {
	        $rows[$key]["iIsActive"] = boolToIcon($row["iIsActive"]);
            $rows[$key]["opts"] = getIcon("?mode=edit&iMedieID=" . $row["iMedieID"], "pencil") .
                    getIcon("?mode=details&iMedieID=" . $row["iMedieID"], "eye") .
                    getIcon("", "trash", "Slet medie", "remove(" . $row["iMedieID"] . ")");
        }
        /* Call list presenter object  */
        $p = new listPresenter($arrLabels, $rows);
        echo $p->presentlist();

        sysFooter();
        break;

    case "DETAILS":
        $iMedieID = filter_input(INPUT_GET, "iMedieID", FILTER_SANITIZE_NUMBER_INT);

        $strModuleMode = "Detaljer";
        sysHeader();
        /* Set array button panel */
        $arrButtonPanel = array();
        //$arrButtonPanel[] = getTopicPicker(crud::MOD_NEWSLETTER,$iMedieID);            
        $arrButtonPanel[] = getButton("button", "Rediger", "getUrl('?mode=edit&iMedieID=" . $iMedieID . "')");
        $arrButtonPanel[] = getButton("button", "Oversigt", "document.location.href='?mode=list'");

        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName, $strModuleMode, $arrButtonPanel);

        $obj = new medie();
        $obj->getitem($iMedieID);

        $obj->arrValues["daCreated"] = time2local($obj->arrValues["daCreated"]);
        $obj->arrValues["iIsActive"] = boolToIcon($obj->arrValues["iIsActive"]);

        $p = new listPresenter($obj->arrColumns, $obj->arrValues);
        echo $p->presentdetails();


        sysFooter();
        break;

    case "EDIT";
        $iMedieID = filter_input(INPUT_GET, "iMedieID", FILTER_SANITIZE_NUMBER_INT);

        $obj = new medie();
        if ($iMedieID > 0) {
            $obj->getitem($iMedieID);
        }

        $strModuleMode = ($iMedieID > 0) ? "Rediger" : "Opret nyt medie";
        sysHeader();

        /* Set array button panel */
        $arrButtonPanel = array();

        if ($iMedieID > 0) {
            $arrButtonPanel[] = getButton("button", "Detaljer", "getUrl('?mode=details&iMedieID=" . $iMedieID . "')");
        }
        $arrButtonPanel[] = getButton("button", "Oversigt", "getUrl('?mode=list')");
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName, $strModuleMode, $arrButtonPanel);

	    /* Call From Presenter */
        $form = new formPresenter($obj->arrColumns, $obj->arrValues);
        echo $form->presentform();

        sysFooter();
        break;

    case "SAVE";
        $medie = new medie();
        $iMedieID = $medie->save();
        header("Location: ?mode=details&iMedieID=" . $iMedieID);
        break;

    case "DELETE":
        $obj = new medie();
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
        $obj->delete($id);
        header("Location: ?mode=list");
        break;
}

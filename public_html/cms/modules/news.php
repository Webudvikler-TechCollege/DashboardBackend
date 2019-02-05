<?php
require_once filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/cms/assets/incl/init.php";
$mode = setMode();
$strModuleName = "Nyheder";

switch (strtoupper($mode)) {
    /* List Mode */
    case "LIST":
        $strModuleMode = "Oversigt";
        sysHeader();
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button", "Opret nyhed", "getUrl('?mode=edit&iNewsID=-1')");
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName, $strModuleMode, $arrButtonPanel);
        /* Fetch artists from DB */
        $nl = new news();
        $rows = $nl->getall();

        $arrLabels = array(
            "opts" => "Options",
            "vcTitle" => $nl->arrColumns["vcTitle"]["Label"],
            "daStart" => $nl->arrColumns["daStart"]["Label"],
            "daCreated" => $nl->arrColumns["daCreated"]["Label"]
        );

        /* Format rows with option icons */
        foreach ($rows as $key => $row) {
            $rows[$key]["opts"] = getIcon("?mode=edit&iNewsID=" . $row["iNewsID"], "pencil") .
                    getIcon("?mode=details&iNewsID=" . $row["iNewsID"], "eye") .
                    getIcon("", "trash", "Slet nyhed", "remove(" . $row["iNewsID"] . ")");
        }
        /* Call list presenter object  */
        $p = new listPresenter($arrLabels, $rows);
        echo $p->presentlist();

        sysFooter();
        break;

    case "DETAILS":
        $iNewsID = filter_input(INPUT_GET, "iNewsID", FILTER_SANITIZE_NUMBER_INT);

        $strModuleMode = "Detaljer";
        sysHeader();
        /* Set array button panel */
        $arrButtonPanel = array();
        //$arrButtonPanel[] = getTopicPicker(crud::MOD_NEWSLETTER,$iNewsID);            
        $arrButtonPanel[] = getButton("button", "Rediger", "getUrl('?mode=edit&iNewsID=" . $iNewsID . "')");
        $arrButtonPanel[] = getButton("button", "Oversigt", "document.location.href='?mode=list'");

        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName, $strModuleMode, $arrButtonPanel);

        $nl = new news();
        $nl->getitem($iNewsID);

        $nl->arrValues["daStart"] = time2local($nl->arrValues["daStart"]);
        $nl->arrValues["daCreated"] = time2local($nl->arrValues["daCreated"]);
        $nl->arrValues["iIsActive"] = boolToIcon($nl->arrValues["iIsActive"]);

        $p = new listPresenter($nl->arrColumns, $nl->arrValues);
        echo $p->presentdetails();


        sysFooter();
        break;

    case "EDIT";
        $iNewsID = filter_input(INPUT_GET, "iNewsID", FILTER_SANITIZE_NUMBER_INT);

        $nl = new news();
        if ($iNewsID > 0) {
            $nl->getitem($iNewsID);
        }

        $strModuleMode = ($iNewsID > 0) ? "Rediger" : "Opret nyt nyhed";
        sysHeader();

        /* Set array button panel */
        $arrButtonPanel = array();

        if ($iNewsID > 0) {
            $arrButtonPanel[] = getButton("button", "Detaljer", "getUrl('?mode=details&iNewsID=" . $iNewsID . "')");
        }
        $arrButtonPanel[] = getButton("button", "Oversigt", "getUrl('?mode=list')");
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName, $strModuleMode, $arrButtonPanel);

        /* Call From Presenter */
        $form = new formPresenter($nl->arrColumns, $nl->arrValues);
        echo $form->presentform();

        sysFooter();
        break;

    case "SAVE";
        $news = new news();
	    $news->arrColumns["daStart"]["Value"] = makeStamp("daStart");
        $iNewsID = $news->save();
        header("Location: ?mode=details&iNewsID=" . $iNewsID);
        break;

    case "DELETE":
        $obj = new news();
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
        $obj->delete($id);
        header("Location: ?mode=list");
        break;
}

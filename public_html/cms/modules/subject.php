<?php
require_once filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/cms/assets/incl/init.php";
$mode = setMode();
$strModuleName = "Fag";

switch (strtoupper($mode)) {
    /* List Mode */
    case "LIST":
        $strModuleMode = "Oversigt";
        sysHeader();
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button", "Opret fag", "getUrl('?mode=edit&iSubjectID=-1')");
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName, $strModuleMode, $arrButtonPanel);
        /* Fetch artists from DB */
        $obj = new mh_subject();
        $rows = $obj->getall();

        $arrLabels = array(
            "opts" => "Options",
            "vcName" => $obj->arrColumns["vcName"]["Label"],
            "vcFriendlyName" => $obj->arrColumns["vcFriendlyName"]["Label"]
        );

        /* Format rows with option icons */
        foreach ($rows as $key => $row) {
            $rows[$key]["opts"] = getIcon("?mode=edit&iSubjectID=" . $row["iSubjectID"], "pencil") .
                    getIcon("?mode=details&iSubjectID=" . $row["iSubjectID"], "eye") .
                    getIcon("", "trash", "Slet nyhed", "remove(" . $row["iSubjectID"] . ")");
        }

        /* Call list presenter object  */
        $p = new listPresenter($arrLabels, $rows);
        echo $p->presentlist();

        sysFooter();
        break;

        case "DETAILS":
        $iSubjectID = filter_input(INPUT_GET, "iSubjectID", FILTER_SANITIZE_NUMBER_INT);

        $strModuleMode = "Detaljer";
        sysHeader();
        /* Set array button panel */
        $arrButtonPanel = array();
        //$arrButtonPanel[] = getTopicPicker(crud::MOD_NEWSLETTER,$iSubjectID);            
        $arrButtonPanel[] = getButton("button", "Rediger", "getUrl('?mode=edit&iSubjectID=" . $iSubjectID . "')");
        $arrButtonPanel[] = getButton("button", "Oversigt", "document.location.href='?mode=list'");

        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName, $strModuleMode, $arrButtonPanel);

        $obj = new mh_subject;
        $obj->getitem($iSubjectID);

        $p = new listPresenter($obj->arrColumns, $obj->arrValues);
        echo $p->presentdetails();


        sysFooter();
        break;

    case "EDIT";
        $iSubjectID = filter_input(INPUT_GET, "iSubjectID", FILTER_SANITIZE_NUMBER_INT);

        $obj = new mh_subject;
        if ($iSubjectID > 0) {
            $obj->getitem($iSubjectID);
        }

        $strModuleMode = ($iSubjectID > 0) ? "Rediger" : "Opret nyt emne";
        sysHeader();

        /* Set array button panel */
        $arrButtonPanel = array();

        if ($iSubjectID > 0) {
            $arrButtonPanel[] = getButton("button", "Detaljer", "getUrl('?mode=details&iSubjectID=" . $iSubjectID . "')");
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
        $news = new mh_subject;
        $iSubjectID = $news->save();
        header("Location: ?mode=details&iSubjectID=" . $iSubjectID);
        break;

    case "DELETE":
        $obj = new mh_subject;
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
        $obj->delete($id);
        header("Location: ?mode=list");
        break;
}

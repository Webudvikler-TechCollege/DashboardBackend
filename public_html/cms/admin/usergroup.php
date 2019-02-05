<?php
require_once filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/cms/assets/incl/init.php";
$mode = setMode();
$strModuleName = "Brugergrupper";

switch(strtoupper($mode)) {
    /* List Mode */
    case "LIST": 
        $strModuleMode = "Oversigt";
        sysHeader();        
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button","Opret ny gruppe","getUrl('?mode=edit&iGroupID=-1')");
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        /* Fetch artists from DB */
        $group = new usergroup();
        $rows = $group->getall();        
        
        $arrLabels = array(
            "opts" => "Options", 
            "vcGroupName" => $group->arrColumns["vcGroupName"]["Label"],
            "daCreated" => "Oprettet"
        );        
        
        /* Format rows with option icons */
        foreach($rows as $key => $row) {
            $rows[$key]["opts"] = getIcon("?mode=edit&iGroupID=" . $row["iGroupID"], "pencil") .
                                        getIcon("?mode=details&iGroupID=" . $row["iGroupID"], "eye");
            if($row["iGroupID"] < 5) {
                $rows[$key]["opts"] .= getIcon("","trash","Systemgruppe - kan ikke slettes!","","dimmed");
            } else {
                $rows[$key]["opts"] .= getIcon("","trash","Slet gruppe","remove(".$row["iGroupID"].")");
            }
        }        
        /* Call list presenter object  */
        $p = new listPresenter($arrLabels,$rows);
        echo $p->presentlist();

        sysFooter();
        break;
    
    case "DETAILS":
        $iGroupID = filter_input(INPUT_GET, "iGroupID", FILTER_SANITIZE_NUMBER_INT);
        
        $strModuleMode = "Detaljer";
        sysHeader();
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getTopicPicker(crud::MOD_USERGROUP,$iGroupID);            
        $arrButtonPanel[] = getButton("button","Rediger","getUrl('?mode=edit&iGroupID=".$iGroupID."')");
        $arrButtonPanel[] = getButton("button","Oversigt","getUrl('?mode=list')");
        
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        $group = new usergroup();
        $group->getitem($iGroupID);
        $group->arrValues["daCreated"] = time2local($group->arrValues["daCreated"]);
        
        $p = new listPresenter($group->arrColumns,$group->arrValues);
        echo $p->presentdetails();
        
        sysFooter();        
        break;

    case "EDIT";
        $iGroupID = filter_input(INPUT_GET, "iGroupID", FILTER_SANITIZE_NUMBER_INT);
        $strModuleMode = ($iGroupID > 0) ? "Rediger" : "Opret ny gruppe";
        sysHeader();
        
        /* Set array button panel */
        $arrButtonPanel = array();
        if($iGroupID > 0) {
            $arrButtonPanel[] = getButton("button","Detaljer","getUrl('?mode=details&iGroupID=".$iGroupID."')");
        }
        $arrButtonPanel[] = getButton("button","Oversigt","getUrl('?mode=list')");
        
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        $group = new usergroup();
        if($iGroupID > 0) { 
            $group->getitem($iGroupID); 
            if($iGroupID < 5) {
                $group->arrColumns["vcRoleName"]["Formtype"] = crud::INPUT_TEXT_READONLY;
            }
        }

        /* Call From Presenter */
        $form = new formPresenter($group->arrColumns,$group->arrValues);
        echo $form->presentform();
        
        sysFooter();
        break;

    case "SAVE";
        $group = new usergroup();
        $iGroupID = $group->save();
        header("Location: ?mode=details&iGroupID=" . $iGroupID);
        break;
    
    case "DELETE":
        $obj = new usergroup();
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
        $obj->delete($id);
        header("Location: ?mode=list");
        break;    
}

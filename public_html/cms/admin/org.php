<?php
require_once filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/cms/assets/incl/init.php";
$mode = setMode();
$strModuleName = "Organisationer";

switch(strtoupper($mode)) {
    /* List Mode */
    case "LIST": 
        $strModuleMode = "Oversigt";
        sysHeader();        
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button","Opret ny organisation","getUrl('?mode=edit&iOrgID=-1')");
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        /* Fetch artists from DB */
        $org = new org();
        $rows = $org->getall();        
        
        $arrLabels = array(
            "opts" => "Options", 
            "vcOrgName" => $org->arrColumns["vcOrgName"]["Label"],
            "vcEmail" => $org->arrColumns["vcEmail"]["Label"]
        );        
        

        /* Format rows with option icons */
        foreach($rows as $key => $row) {
            $rows[$key]["opts"] = getIcon("?mode=edit&iOrgID=" . $row["iOrgID"], "pencil") .
                                        getIcon("?mode=details&iOrgID=" . $row["iOrgID"], "eye") .
                                        getIcon("","trash","Slet bruger","remove(".$row["iOrgID"].")");
        }        
        /* Call list presenter object  */
        $p = new listPresenter($arrLabels,$rows);
        echo $p->presentlist();

        sysFooter();
        break;
    
    case "DETAILS":
        $iOrgID = filter_input(INPUT_GET, "iOrgID", FILTER_SANITIZE_NUMBER_INT);
        
        $strModuleMode = "Detaljer";
        sysHeader();
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getTopicPicker(crud::MOD_ORG,$iOrgID);            
        $arrButtonPanel[] = getButton("button","Rediger","getUrl('?mode=edit&iOrgID=".$iOrgID."')");
        $arrButtonPanel[] = getButton("button","Oversigt","document.location.href='?mode=list'");
        
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        $org = new org();
        $org->getitem($iOrgID);
        
        $p = new listPresenter($org->arrColumns,$org->arrValues);
        echo $p->presentdetails();
        
        sysFooter();        
        break;

    case "EDIT";
        $iOrgID = filter_input(INPUT_GET, "iOrgID", FILTER_SANITIZE_NUMBER_INT);
        $strModuleMode = ($iOrgID > 0) ? "Rediger" : "Opret ny organisation";
        sysHeader();
        
        /* Set array button panel */
        $arrButtonPanel = array();
        if($iOrgID > 0) {
            $arrButtonPanel[] = getButton("button","Detaljer","getUrl('?mode=details&iOrgID=".$iOrgID."')");
        }        
        $arrButtonPanel[] = getButton("button","Oversigt","getUrl('?mode=list')");
        
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        $org = new org();
        if($iOrgID > 0) { $org->getitem($iOrgID); }
        /* Call From Presenter */
        $form = new formPresenter($org->arrColumns,$org->arrValues);
        echo $form->presentform();
        
        sysFooter();
        break;

    case "SAVE";
        $org = new org();
        $iOrgID = $org->save();
        header("Location: ?mode=details&iOrgID=" . $iOrgID);
        break;
    
    case "DELETE":
        $obj = new org();
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
        $obj->delete($id);
        header("Location: ?mode=list");
        break;    
}

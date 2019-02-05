<?php
require_once filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/cms/assets/incl/init.php";
$mode = setMode();
$strModuleName = "Brugere";

switch(strtoupper($mode)) {
    /* List Mode */
    case "LIST": 
        $strModuleMode = "Oversigt";
        sysHeader();        
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button","Opret ny bruger","getUrl('?mode=edit&iUserID=-1')");
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        /* Fetch artists from DB */
        $user = new user();
        $rows = $user->getall();        
        
        $arrLabels = array(
            "opts" => "Options", 
            "vcUserName" => $user->arrColumns["vcUserName"]["Label"],
            "vcFirstName" => $user->arrColumns["vcFirstName"]["Label"],
            "vcLastName" => $user->arrColumns["vcLastName"]["Label"],
            "vcEmail" => $user->arrColumns["vcEmail"]["Label"]
        );        
        

        /* Format rows with option icons */
        foreach($rows as $key => $row) {
            $rows[$key]["opts"] = getIcon("?mode=edit&iUserID=" . $row["iUserID"], "pencil") .
                                        getIcon("?mode=details&iUserID=" . $row["iUserID"], "eye") .
                                        getIcon("","trash","Slet bruger","remove(".$row["iUserID"].")");
        }        
        /* Call list presenter object  */
        $p = new listPresenter($arrLabels,$rows);
        echo $p->presentlist();

        sysFooter();
        break;
    
    case "DETAILS":
        
        $iUserID = filter_input(INPUT_GET, "iUserID", FILTER_SANITIZE_NUMBER_INT);
        
        $strModuleMode = "Detaljer";
        sysHeader();
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getTopicPicker(crud::MOD_USER,$iUserID);            
        $arrButtonPanel[] = getButton("button","Vælg grupper","getUrl('?mode=setusergroups&iUserID=".$iUserID."')");
        $arrButtonPanel[] = getButton("button","Rediger","getUrl('?mode=edit&iUserID=".$iUserID."')");
        $arrButtonPanel[] = getButton("button","Oversigt","document.location.href='?mode=list'");
        
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        $user = new user();
        $user->getitem($iUserID);
                
        $user->arrValues["iOrgID"] = $user->org["vcOrgName"];
        $user->arrValues["vcImage"] = "<img src=\"".$user->arrValues["vcImage"]."\">";
        $user->arrValues["iSuspended"] = boolToText($user->arrValues["iSuspended"]);
        unset($user->arrColumns["vcPassword"]);
                
        if(count($user->arrValues["arrUserGroups"])) {
            $user->arrColumns["arrUserGroups"] = array("Label" => "Brugergrupper");
            $user->arrValues["arrUserGroups"] = implode(array_column($user->arrValues["arrUserGroups"], "vcGroupName"),", ");
        }
        
        $p = new listPresenter($user->arrColumns,$user->arrValues);
        echo $p->presentdetails();
        
        sysFooter();        
        break;

    case "EDIT";
        $iUserID = filter_input(INPUT_GET, "iUserID", FILTER_SANITIZE_NUMBER_INT);
        $strModuleMode = ($iUserID > 0) ? "Rediger" : "Opret ny bruger";
        sysHeader();
        
        /* Set array button panel */
        $arrButtonPanel = array();
        if($iUserID > 0) {
            $arrButtonPanel[] = getButton("button","Detaljer","getUrl('?mode=details&iUserID=".$iUserID."')");
        }
        $arrButtonPanel[] = getButton("button","Oversigt","getUrl('?mode=list')");
        
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        $user = new user();
        if($iUserID > 0) { $user->getitem($iUserID); }
        
        $org = new Org();
        $arrOrgOpts = $org->getoptions();
        array_unshift($arrOrgOpts, array("","Vælg organisation"));
        $user->arrValues["iOrgID"] = SelectBox("iOrgID", $arrOrgOpts, $user->arrValues["iOrgID"]);        
        
        /* Call From Presenter */
        $form = new formPresenter($user->arrColumns,$user->arrValues);
        echo $form->presentform();
        
        sysFooter();
        break;

    case "SAVE";
        $user = new user();
        $iUserID = $user->save();
        header("Location: ?mode=details&iUserID=" . $iUserID);
        break;
    
    case "DELETE":
        $obj = new User();
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
        $obj->delete($id);
        header("Location: ?mode=list");
        break;
    
    case "SETUSERGROUPS":
        $iUserID = filter_input(INPUT_GET, "iUserID", FILTER_SANITIZE_NUMBER_INT);
        
        $strModuleMode = "Bruger grupper";
        sysHeader();
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button","Detaljer","getUrl('?mode=details&iUserID=".$iUserID."')");
        $arrButtonPanel[] = getButton("button","Oversigt","document.location.href='?mode=list'");
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        /* Create array with user related groups */
        $arrSelected = array();
        $params = array($iUserID);
        $strSelect = "SELECT iGroupID FROM usergrouprel WHERE iUserID = ?";
        $rel = $db->_fetch_array($strSelect, $params);
        foreach($rel as $value) {
            $arrSelected[] = $value["iGroupID"];
        }
        
        /* Get all groups from group object */
        $group = new usergroup();
        $rows = $group->getall();

        /* Define arrColumns with user id field */
        $arrColumns = array();
        $arrColumns["iUserID"] = array(
            "Formtype" => crud::INPUT_HIDDEN,
            "Required" => 0
        );
        
        /* Defien form values with user id value */
        $arrFormValues = array();
        $arrFormValues["iUserID"] = $iUserID;
                
        /* Loop rows and define arrColumns with checkboxes for group id's */
        foreach($rows as $key => $arrValues) {
            $field = "groups[".$arrValues["iGroupID"]."]";
            $arrColumns[$field] = array(
                    "Formtype" => crud::INPUT_CHECKBOX,
                    "Required" => 0,
                    "Label" => $arrValues["vcGroupName"]
                );
            /* Set form values with related group id's */
            $arrFormValues[$field] = in_array($arrValues["iGroupID"], $arrSelected) ? 1 : 0;            
        }
        
        $form = new formPresenter($arrColumns,$arrFormValues);
        $form->formAction = "saveusergroups";
        echo $form->presentform();

        sysFooter();        
        break;
        
    case "SAVEUSERGROUPS":
        $iUserID = filter_input(INPUT_POST, "iUserID", FILTER_SANITIZE_NUMBER_INT);
                
        /* Delete existing user related groups */
        $params = array($iUserID);
        $strDelete = "DELETE FROM usergrouprel WHERE iUserID = ?";
        $db->_query($strDelete, $params);
        
        /* Create array for post filtering */
        $args = array(
                    "groups" => array(
                        "filter" => FILTER_VALIDATE_INT,
                        "flags" => FILTER_REQUIRE_ARRAY
                        )
                    );
        $arrInputVal = filter_input_array(INPUT_POST, $args);
        
        /* Save user related groups if any */
        if(count($arrInputVal["groups"])) {
            $arrGroups = array_keys($arrInputVal["groups"]);
            foreach($arrGroups as $value) {
                $params = array($iUserID, $value);
                $strInsert = "INSERT INTO usergrouprel(iUserID, iGroupID) VALUES(?,?)";
                $db->_query($strInsert, $params);
            }
        }
        
        header("Location: ?mode=details&iUserID=" . $iUserID);
        
        break;
}

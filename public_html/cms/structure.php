<?php
require_once filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/cms/assets/incl/init.php";
$mode = setMode();
$strModuleName = "Website Struktur";

switch(strtoupper($mode)) {
    /* List Mode */
    case "LIST": 
        $iParentID = filter_input(INPUT_GET,"iParentID",FILTER_SANITIZE_NUMBER_INT, getDefaultValue(-1));
        
        $strModuleMode = "Oversigt";
        sysHeader();        
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button","Opret ny folder","getUrl('?mode=folderedit&iFolderID=-1&iParentID=".$iParentID."')");
        $arrButtonPanel[] = getButton("button","Opret ny side","getUrl('?mode=pageedit&iPageID=-1&iParentID=".$iParentID."')");
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        /* Fetch artists from DB */
        $page = new page();
        $rows = $page->listbyparent($iParentID);
        
        foreach($rows as $key => $row) {
            $rows[$key]["opts"] = getIcon("?mode=pageedit&iPageID=" . $row["iPageID"], "pencil")
                                    . getIcon("?mode=pagedetails&iPageID=" . $row["iPageID"], "eye");
            $rows[$key]["opts"] .= ($rows[$key]["iIsStartPage"]) ?  
                getIcon("Javascript:void(0)","trash","Slet side","", "dimmed") : 
                getIcon("","trash","Slet side","remove(".$row["iPageID"].",'?mode=pagedelete&id=".$row["iPageID"]."')");
            $rows[$key]["opts"] .= ($row["iIsStartPage"] > 0) ?
                    getIcon("?mode=pagesetstart&iPageID=".$row["iPageID"],"home","Startside") :
                    getIcon("?mode=pagesetstart&iPageID=".$row["iPageID"],"home","","","icon dimmed");
            $rows[$key]["opts"] .= ($row["iIsActive"] > 0) ? 
                    getIcon("?mode=pagestatusedit&iPageID=".$row["iPageID"],"pause-circle","Stop", "", "active") : 
                    getIcon("?mode=pagestatusedit&iPageID=".$row["iPageID"],"play-circle","Start", "", "paused");

            $rows[$key]["vcTitle"] = $row["iIsActive"] > 0 ? 
                                        "<span class=\"online\">" . $row["vcTitle"] . "</span>" : 
                                        "<span class=\"offline\">" . $row["vcTitle"] . "</span>";            
        }        
            
        $f = new FolderTree($iParentID,"folder","Website");
        echo $f->buildTree();
        
        $p = new listPresenter($page->arrLabels,$rows);
        $p->hasSortOnSave = TRUE;
        echo $p->presentlist();
        
        sysFooter();
        break;
        
    case "SAVESORT":
        $args = array(
                    "sortId" => array(
                        "filter" => FILTER_VALIDATE_INT,
                        "flags" => FILTER_REQUIRE_ARRAY
                        )
                    );
        $arrSortNums = filter_input_array(INPUT_POST, $args);
        foreach($arrSortNums["sortId"] as $iPageID => $iSortNum) {
            $params = array($iSortNum, $iPageID);
            $strUpdate = "UPDATE page SET iSortNum = ? WHERE iPageID = ?";
            $db->_query($strUpdate, $params);
        }
        
        header("Location: ?mode=list&iParentID=" . $_GET["iParentID"]);
        break;
    
    case "PAGEDETAILS":
        $iPageID = filter_input(INPUT_GET, "iPageID", FILTER_SANITIZE_NUMBER_INT);
        
        $strModuleMode = "Detaljer";
        sysHeader();
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getTopicPicker(crud::MOD_PAGE,$iPageID);            
        $arrButtonPanel[] = getButton("button","Layout","getUrl('?mode=pagelayout&iPageID=".$iPageID."')");
        $arrButtonPanel[] = getButton("button","Rediger","getUrl('?mode=pageedit&iPageID=".$iPageID."')");
        $arrButtonPanel[] = getButton("button","Oversigt","getUrl('?mode=list')");
        
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        $page = new page();
        $page->getitem($iPageID);
        
        $folder = new folder();
       
        $page->arrValues["vcUrlName"] = $folder->getUrlPath($page->iParentID) . "/" . $page->vcUrlName . ".htm";
        $page->arrValues["iParentID"] = folder::getfoldername($page->iParentID);
        $page->arrValues["daCreated"] = time2local($page->arrValues["daCreated"]);
        $page->arrValues["daStart"] = time2local($page->arrValues["daStart"]);
        $page->arrValues["daStop"] = time2local($page->arrValues["daStop"]);
        $page->arrValues["iIsStartPage"] = boolToIcon($page->arrValues["iIsStartPage"]);
        $page->arrValues["iIsActive"] = boolToIcon($page->arrValues["iIsActive"]);
        
        $p = new listPresenter($page->arrColumns,$page->arrValues);
        echo $p->presentdetails();
        
        sysFooter();        
        break;

    case "PAGEEDIT";
        $iPageID = filter_input(INPUT_GET, "iPageID", FILTER_SANITIZE_NUMBER_INT);
        $iParentID = filter_input(INPUT_GET, "iParentID", FILTER_SANITIZE_NUMBER_INT);
        $strModuleMode = ($iPageID > 0) ? "Rediger side" : "Opret ny side";
        sysHeader();
        
        /* Set array button panel */
        $arrButtonPanel = array();
        if($iPageID > 0) {
            $arrButtonPanel[] = getButton("button","Detaljer","getUrl('?mode=pagedetails&iPageID=".$iPageID."')");
        }        
        $arrButtonPanel[] = getButton("button","Oversigt","document.location.href='?mode=list'");
        
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        $page = new page();
        if($iPageID > 0) { 
            $page->getitem($iPageID);
        } else {
            $page->arrValues["iSortNum"] = $page->getNewSortNum($iParentID);
        }
        $page->arrValues["iParentID"] = ($iPageID > 0) ? $page->arrValues["iParentID"] : $iParentID;
        
        $arrFolderOpts = array();
        $arrFolderOpts = folder::getFolderOpts(-1, $arrFolderOpts);
        $page->arrValues["iParentID"] = SelectBox("iParentID", $arrFolderOpts, $page->arrValues["iParentID"]);        
                
        /* Call From Presenter */
        $form = new formPresenter($page->arrColumns,$page->arrValues);
        $form->formAction = "pagesave";
        echo $form->presentform();
?>
        <script>
            $('#vcUrlName').focus( function() {
                strName = $('#vcTitle').val();
                
                strUrlName = getWebsafeStr(strName);
                $('#vcUrlName').val(strUrlName);
            })
        </script>
<?php
        sysFooter();
        break;

    case "PAGESAVE";
        $page = new page();
        $page->arrColumns["daStart"]["Value"] = makeStamp("daStart");
        $page->arrColumns["daStop"]["Value"] = makeStamp("daStop");
        $iPageID = $page->save();
        header("Location: ?mode=pagedetails&iPageID=" . $iPageID);
        break;
    
    case "PAGESTATUSEDIT":
        $iPageID = filter_input(INPUT_GET, "iPageID", FILTER_SANITIZE_NUMBER_INT);
        
        $obj = new page;
        $obj->getItem($iPageID);

        if($obj->arrColumns["iIsActive"]["Value"] > 0) {
            $obj->arrColumns["iIsActive"]["Value"] = 0;
        } else {
            $obj->arrColumns["iIsActive"]["Value"] = 1;
        }
        
        $obj->save();
        
        header("Location: ?mode=list&iParentID=".$obj->iParentID);
    break;
    
    case "PAGESETSTART":
        $iPageID = filter_input(INPUT_GET, "iPageID", FILTER_SANITIZE_NUMBER_INT);
        $strModuleMode = "Vælg startside";
        sysHeader();
        
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button","Oversigt","document.location.href='?mode=list'");
        
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        $page = new page();
        $page->getItem($iPageID);
        
        $arrColumns = array(
            "iPageID" => $page->arrColumns["iPageID"],
            "iParentID" => $page->arrColumns["iParentID"]
        );
        
        $arrColumns["iPageID"]["Formtype"] = "select";
        $arrColumns["iPageID"]["Label"] = "Vælg side";

        $arrColumns["iParentID"]["Formtype"] = "hidden";
        
        $arrPageOpts = $page->listbyparent($page->iParentID);
        $page->arrValues["iPageID"] = SelectBox("iPageID", $arrPageOpts, $page->arrValues["iPageID"]);
                        
        /* Call From Presenter */
        $form = new formPresenter($arrColumns,$page->arrValues);
        $form->formAction = "pagesavestart";
        echo $form->presentform();
    break;    

    case "PAGESAVESTART":
        $iPageID = filter_input(INPUT_POST, "iPageID", FILTER_SANITIZE_NUMBER_INT, getDefaultValue(-1));
        $iParentID = filter_input(INPUT_POST, "iParentID", FILTER_SANITIZE_NUMBER_INT, getDefaultValue(-1));
        
        $params = array($iParentID);
        $strReset = "UPDATE page SET iIsStartPage = 0 WHERE iParentID = ?";
        $db->_query($strReset,$params);

        $params = array($iPageID);
        $strSet = "UPDATE page SET iIsStartPage = 1 WHERE iPageID = ?";
        $db->_query($strSet,$params);
                
        header("Location: ?mode=list&iParentID=".$iParentID);
    break; 
    
    case "PAGEDELETE";
        $obj = new page();
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
        $obj->getItem($id);
        $obj->delete($id);
        header("Location: ?mode=list&iParentID=" . $obj->iParentID);
        break;    
    
    case "FOLDERDETAILS":
        $iFolderID = filter_input(INPUT_GET, "iFolderID", FILTER_SANITIZE_NUMBER_INT);
        
        $strModuleMode = "Folder Detaljer";
        sysHeader();
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button","Opret ny folder","getUrl('?mode=folderedit&iFolderID=-1')");
        $arrButtonPanel[] = getButton("button","Rediger","getUrl('?mode=folderedit&iFolderID=".$iFolderID."')");
        $arrButtonPanel[] = getButton("button","Oversigt","getUrl('?mode=list&iParentID=".$iFolderID."')");
        
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        $folder = new folder();
        $folder->getitem($iFolderID);

        $folder->arrValues["vcUrlName"] = $folder->getUrlPath($iFolderID);        
        $folder->arrValues["iParentID"] = folder::getfoldername($folder->arrValues["iParentID"]);
        $folder->arrValues["iDesignID"] = design::getname($folder->arrValues["iDesignID"]);
        $folder->arrValues["daCreated"] = date2local($folder->arrValues["daCreated"],SHOWHOURS);
        $folder->arrValues["iIsActive"] = boolToIcon($folder->arrValues["iIsActive"]);
        
        $p = new listPresenter($folder->arrColumns,$folder->arrValues);
        echo $p->presentdetails();
        
        sysFooter();        
        break;

    case "FOLDEREDIT";
        $iFolderID = filter_input(INPUT_GET, "iFolderID", FILTER_SANITIZE_NUMBER_INT);
        $iParentID = filter_input(INPUT_GET, "iParentID", FILTER_SANITIZE_NUMBER_INT);
        $strModuleMode = ($iFolderID > 0) ? "Rediger" : "Opret ny folder";
        sysHeader();
        
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button","Oversigt","document.location.href='?mode=list'");
        
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        $folder = new folder();
        
        if($iFolderID > 0) { 
            $folder->getitem($iFolderID);
        } else {
            $folder->arrValues["iSortNum"] = $folder->getNewSortNum($iParentID);
            $folder->arrValues["iParentID"] = $iParentID;
        }
        
        $arrFolderOpts = array();
        $arrFolderOpts = folder::getFolderOpts(-1, $arrFolderOpts);
        $folder->arrValues["iParentID"] = SelectBox("iParentID", $arrFolderOpts, $folder->arrValues["iParentID"]);
        
        $arrDesignOpts = array(0 => array(1,"Test Design"));
        $folder->arrValues["iDesignID"] = SelectBox("iDesignID", $arrDesignOpts, $folder->arrValues["iDesignID"]);        
                
        /* Call From Presenter */
        $form = new formPresenter($folder->arrColumns,$folder->arrValues);
        $form->formAction = "foldersave";
        echo $form->presentform();
?>
        <script>
            $('#vcUrlName').focus( function() {
                strName = $('#vcTitle').val();
                
                strUrlName = getWebsafeStr(strName);
                $('#vcUrlName').val(strUrlName);
            })
        </script>
<?php        
        sysFooter();
        break;

    case "FOLDERSAVE";
        $folder = new folder();
        $iFolderID = $folder->save();

        $saveMode = filter_input(INPUT_POST, "iFolderID", FILTER_SANITIZE_NUMBER_INT);
        if($saveMode < 0) {
            $page = new page();
            $page->arrColumns["iPageID"]["Value"] = -1;
            $page->arrColumns["iParentID"]["Value"] = $iFolderID;
            $page->arrColumns["vcTitle"]["Value"] = $folder->arrColumns["vcTitle"]["Value"];
            $page->arrColumns["vcUrlName"]["Value"] = getWebsafe($folder->arrColumns["vcTitle"]["Value"]);
            $page->arrColumns["daStart"]["Value"] = time();
            $page->arrColumns["daStop"]["Value"] = time();
            $page->arrColumns["iIsStartPage"]["Value"] = 1;
            $page->arrColumns["iIsActive"]["Value"] = 0;
            $page->arrColumns["iSortNum"]["Value"] = 1;
            $page->create();
        }
        
        header("Location: ?mode=folderdetails&iFolderID=" . $iFolderID);
        break;
    
    case "FOLDERDELETE";
        $obj = new folder();
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
        $obj->getItem($id);
        $obj->delete($id);
        header("Location: ?mode=list&iParentID=" . $obj->iParentID);
        break;
}

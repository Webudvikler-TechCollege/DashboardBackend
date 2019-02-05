<?php
require_once filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/cms/assets/incl/init.php";
$mode = setMode();
$strModuleName = "Emner";

switch(strtoupper($mode)) {
    /* List Mode */
    case "LIST": 
        $strModuleMode = "Oversigt";
        sysHeader();        
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button","Opret nyt emne","getUrl('?mode=topicedit&iTopicID=-1')");
        $arrButtonPanel[] = getButton("button","Opret ny emnegruppe","getUrl('?mode=groupedit&iGroupID=-1')");
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        /* Fetch artists from DB */
        $group = new topicgroup();
        $rows = $group->getall();      
        $topic = new topic();
        
        
        $arrValues = array();
        /* Format rows with option icons */
        foreach($rows as $key => $row) {
            $rows[$key]["opts"] = getIcon("?mode=groupedit&iGroupID=" . $row["iTopicGroupID"], "pencil") .
                                        getIcon("","trash","Slet gruppe","remove(".$row["iTopicGroupID"].",'?mode=groupdelete&id=".$row["iTopicGroupID"]."')");
            $rows[$key]["vcTitle"] = "<b>" . $rows[$key]["vcTitle"] . "</b>";
            $arrValues[] = $rows[$key];
            $topics = $topic->listbyparent($row["iTopicGroupID"]);
            $arrTopics = array();
            foreach($topics as $tkey => $trow) {
                $arrTopics = array(
                                "opts" => getIcon("?mode=topicedit&iTopicID=" . $trow["iTopicID"], "pencil") .
                                            getIcon("","trash","Slet emne","remove(".$trow["iTopicID"].",'?mode=topicdelete&id=".$trow["iTopicID"]."')"),
                                "vcTitle" => "&nbsp;&nbsp;&raquo;&nbsp;" . $trow["vcTopicName"]
                    );
                $arrValues[] = $arrTopics;
            }
        }        
        
        /* Call list presenter object  */
        $p = new listPresenter($group->arrLabels,$arrValues);
        echo $p->presentlist();

        sysFooter();
        break;

    case "GROUPEDIT";
        $iGroupID = filter_input(INPUT_GET, "iGroupID", FILTER_SANITIZE_NUMBER_INT);
        $strModuleMode = ($iGroupID > 0) ? "Rediger gruppe" : "Opret ny gruppe";
        sysHeader();
        
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button","Oversigt","getUrl('?mode=list')");
        
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        $group = new topicgroup();
        if($iGroupID > 0) { $group->getitem($iGroupID); }
        /* Call From Presenter */
        $form = new formPresenter($group->arrColumns,$group->arrValues);
        $form->formAction = "groupsave";
        echo $form->presentform();
        
        sysFooter();
        break;

    case "GROUPSAVE";
        $group = new topicgroup();
        $iGroupID = $group->save();
        header("Location: ?mode=list");
        break;
    
    case "GROUPDELETE":
        $group = new topicgroup();
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
        $group->delete($id);
        header("Location: ?mode=list");
        break;    
    
    case "TOPICEDIT";
        $iTopicID = filter_input(INPUT_GET, "iTopicID", FILTER_SANITIZE_NUMBER_INT);
        $strModuleMode = ($iTopicID > 0) ? "Rediger emne" : "Opret nyt emne";
        sysHeader();
        
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button","Oversigt","getUrl('?mode=list')");
        
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        $topic = new topic();
        if($iTopicID > 0) { $topic->getitem($iTopicID); }
        
        $group = new topicgroup();
        $arrGroupOpts = $group->getAll();
        $topic->arrValues["iTopicGroupID"] = SelectBox("iTopicGroupID", $arrGroupOpts, $topic->arrValues["iTopicGroupID"]);
        
        /* Call From Presenter */
        $form = new formPresenter($topic->arrColumns,$topic->arrValues);
        $form->formAction = "topicsave";
        echo $form->presentform();
        
        sysFooter();
        break;

    case "TOPICSAVE";
        $topic = new topic();
        $iTopicID = $topic->save();
        header("Location: ?mode=list");
        break;
    
    case "TOPICDELETE":
        $topic = new topic();
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
        $topic->delete($id);
        header("Location: ?mode=list");
        break;    
}

<?php
require_once filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/cms/assets/incl/init.php";
$mode = setMode();
$strModuleName = "Indstillinger";

switch(strtoupper($mode)) {
    /* List Mode */
    case "LIST": 
        $iModule = filter_input(INPUT_GET, "mod", FILTER_SANITIZE_NUMBER_INT, getDefaultValue(crud::MOD_CORE));
        $strModuleMode = "Oversigt";
        sysHeader();
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button","Rediger","getUrl('?mode=edit&mod=".$iModule."')");        
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        /* Fetch artists from DB */
        $conf = new config();
        $rows = $conf->getallByModule($iModule);
        
        
        foreach($rows as $key => $arrValues) {
            if(in_array("iSiteAdminID", $arrValues)) {
                $rows[$key]["value"] = user::getname($rows[$key]["value"]);
            };
            if(in_array("iDesignID", $arrValues)) {
                $rows[$key]["value"] = design::getname($rows[$key]["value"]);
            };
        }
        
        $arrLabels = array(
            "vcFriendlyName" => "Indstilling",
            "value" => "Værdi"
        );
        
        /* Call list presenter object  */
        $p = new listPresenter($arrLabels,$rows);
        $p->strListClass = "no-opt-table";
        echo $p->presentlist();

        sysFooter();
        break;

    case "EDIT";
        sysHeader();
        $iModule = filter_input(INPUT_GET, "mod", FILTER_SANITIZE_NUMBER_INT, getDefaultValue(crud::MOD_CORE));
        
        /* Set array button panel */
        $arrButtonPanel = array();
        $arrButtonPanel[] = getButton("button","Oversigt","getUrl('?mode=list')");
        
        /* Call static panel with title and button options */
        echo textPresenter::presentpanel($strModuleName,$strModuleMode,$arrButtonPanel);
        
        $conf = new config();
        $conf->getallByModule($iModule);
        
        $user = new user();
        $arrUserOpts = $user->getoptions();
        array_unshift($arrUserOpts, array("","Vælg bruger"));
        $conf->arrValues["iSiteAdminID"] = SelectBox("iSiteAdminID", $arrUserOpts, $conf->arrValues["iSiteAdminID"]);

        /* Call From Presenter */
        $form = new formPresenter($conf->arrColumns,$conf->arrValues);
        echo $form->presentform();
        
        sysFooter();
        break;

    case "SAVE";
        $conf = new config();
        $conf->save();
        header("Location: ?mode=list");
        break;
}

<?php
/**
 * File Editor 
 * Creates a editor for file & image handling
 */
require_once filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/cms/assets/incl/init.php";

$mode = filter_input(INPUT_POST,"mode",FILTER_SANITIZE_STRING);
if(empty($mode)) { $mode = filter_input(INPUT_GET,"mode",FILTER_SANITIZE_STRING); }
if(empty($mode)) { $mode = "list"; }

switch(strtoupper($mode)) {
    default:
    case "LIST":
?>
        <div class="modal fade" tabindex="-1" id="topicpicker" role="dialog">
            <input type="hidden" id="elmType" />
            <input type="hidden" id="elmId" />
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <?php echo getButton("button", "X", "", "btn-xs btn-default pull-right", "data-dismiss=\"modal\"") ?>
                        <h4 class="modal-title">Emne vælger</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3 groupmenu">
                                <p><b>Emnegrupper</b></p>
                               <?php
                                    $groups = new topicgroup();
                                    $rows = $groups->getall();
                                    foreach($rows as $key => $arrValues) {
                                        echo "<li><a href=\"javascript:void(0)\" onclick=\"showtopics(".$arrValues["iTopicGroupID"].")\">" . $arrValues["vcTitle"] . "</a></li>";
                                    }
                               ?>
                            </div>
                            <div class="col-md-9" id="topicview">

                            </div>
                        </div>                        

                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        <script src="/cms/assets/js/topicpicker.js?nc=<?php echo time() ?>"></script>
<?php
        break;

    /* Returns a list of files from the given folder path */
    case "LISTTOPICS":
        $iGroupID = filter_input(INPUT_POST, "groupId", FILTER_SANITIZE_NUMBER_INT);
        $elmId = filter_input(INPUT_POST, "elmId", FILTER_SANITIZE_NUMBER_INT);
        $elmType = filter_input(INPUT_POST, "elmType", FILTER_SANITIZE_NUMBER_INT);
        
        $topics = new topic();
        $rows = $topics->listbyparent($iGroupID);
        $arrSelected = $topics->listtopicrel($elmId, $elmType);
        
        $arrTopics = array();
        foreach($rows as $key => $arrValues) {
            $selected = in_array($arrValues["iTopicID"], array_column($arrSelected,"iTopicID")) ? 1 : 0;
            $arrTopics[] = array(
                "topicId" => $arrValues["iTopicID"],
                "topicName" => $arrValues["vcTopicName"],
                "isChecked" => $selected
            );
        }
        echo json_encode($arrTopics);
    break;
    
    /* Returns a list of files from the given folder path */
    case "GETSELECTED":
        $elmId = filter_input(INPUT_POST, "elmId", FILTER_SANITIZE_NUMBER_INT);
        $elmType = filter_input(INPUT_POST, "elmType", FILTER_SANITIZE_NUMBER_INT);
        $topics = new topic();
        $arrSelected = $topics->listtopicrel($elmId, $elmType);
        $arrSelected = array_column($arrSelected, "iTopicID");
        echo json_encode($arrSelected);
    break;    
    
    case "SAVETOPICS":
        $elmType = filter_input(INPUT_POST, "elmType", FILTER_SANITIZE_STRING);
        $elmId = filter_input(INPUT_POST, "elmId", FILTER_SANITIZE_STRING);
        $strTopics = filter_input(INPUT_POST, "arrtopics", FILTER_SANITIZE_STRING);
        $arrTopics = explode(",",$strTopics);
        
        $params = array($elmId,$elmType);
        $strDelete = "DELETE FROM topicrel WHERE iElementID = ? AND iType = ?";
        $db->_query($strDelete, $params);
        
        foreach($arrTopics as $value) {
            $params = array($value,$elmId,$elmType);
            $strInsert = "INSERT INTO topicrel(iTopicID,iElementID,iType) VALUES(?,?,?)";
            $db->_query($strInsert, $params);
        };
        break;
        
    case "GETDEFAULTID":
        $strSelect = "SELECT MIN(iTopicGroupID) FROM topicgroup WHERE iDeleted = 0";
        echo $db->_fetch_value($strSelect);
        break;
}               
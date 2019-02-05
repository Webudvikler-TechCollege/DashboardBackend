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
        <div class="modal fade" tabindex="-1" id="fileeditor" role="dialog">
            <input type="hidden" id="root" />
            <input type="hidden" id="target" />
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <?php echo getButton("button", "X", "", "btn-xs btn-default pull-right", "data-dismiss=\"modal\"") ?>
                        <?php echo getButton("button", "Upload", "prepareFileUpload()", "btn-xs btn-default pull-right") ?>
                        <?php echo getButton("button", "Ny folder", "prepareFolderEdit('new')", "btn-xs btn-default pull-right") ?>
                        <?php echo getButton("button", "Rediger folder", "prepareFolderEdit('edit')", "btn-xs btn-success pull-right fld-edit") ?>
                        <?php echo getButton("button", "Slet folder", "prepareFolderDelete()", "btn-xs btn-danger pull-right fld-edit") ?>
                        <h4 class="modal-title">File Editor</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3 foldermenu">

                            </div>
                            <div class="col-md-9" id="fileview">

                            </div>
                        </div>                        

                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        <script src="/cms/assets/js/fileeditor.js?nc=<?php echo time() ?>"></script>
<?php
        break;
    
    /* Returns a list of files from the given folder path */
    case "GETFILELIST":
        $root = filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING);
        $path = filter_input(INPUT_POST, "path", FILTER_SANITIZE_STRING);
        $fullpath = !empty($path) ? $root . $path : ""; 
        $arrDirContent = scandir($fullpath);
        $arrFiles = array();
        foreach($arrDirContent as $key => $value) {
            if(is_file($fullpath . $value)) {
                $arrFiles[] = array(
                    "file" => $path . $value,
                    "filepath" => $fullpath . $value
                );
            }
        }
        echo json_encode($arrFiles);
    break;
    
    case "FILEVIEW":
        $file = filter_input(INPUT_POST, "file", FILTER_SANITIZE_STRING);
        $arrDim = getimagesize(DOCROOT . $file);
        $strHtml = "<div class=\"row\">\n" .
                    "   <div class=\"col-sm-8\"><img class=\"imgview\" src=\"" . $file . "\"></div>\n" .
                    "   <div class=\"col-sm-4\">" .
                    "   <b>Fil: </b><i>" . basename($file) . "</i><br />\n" .
                    "   <b>Bredde:</b> " . $arrDim[0] . " px<br />" . 
                    "   <b>Højde:</b> " . $arrDim[1] . " px<br />" . 
                    "</div>\n";
        echo $strHtml;

        break;
    
    /* File Upload Form */
    case "FILEUPLOADFORM":
        $f = new FileUpload();
        $strHtml = "<h2>Upload filer</h2>\n" . 
                    "<form method=\"post\" id=\"uploadform\" class=\"form-horizontal\" enctype=\"multipart/form-data\">\n" . 
                    "<fieldset>\n" .
                    "<input type=\"hidden\" name=\"mode\" value=\"savefileupload\">" . 
                    "<input type=\"hidden\" name=\"maxsize\" value=\"".$f->max_file_size."\">" . 
                    "<div class=\"form-group\" data-group=\"file\">\n" .
                    "   <label class=\"col-sm-2 control-label for=\"file\">Vælg fil:</label>\n" . 
                    "   <div class=\"col-sm-10\">\n" . 
                    "       <input type=\"file\" class=\"form-control file-upload\" name=\"files[]\" id=\"files\" value=\"\" multiple=\"\" onchange=\"listFileUpload(this)\" />\n" . 
                    "   </div>\n" . 
                    "</div>\n" .
                    "<div class=\"form-group\" data-group=\"files\" id=\"uploadlist\">\n" .
                    "   <label class=\"col-sm-2 control-label for=\"files\"></label>\n" . 
                    "   <div class=\"col-sm-10\" id=\"uploadlist\">\n" . 
                    "   </div>\n" . 
                    "</div>\n" .
                    "</fieldset>\n" .
                    "</form>\n";
        echo $strHtml;
    break;

    /* Save File Upload */
    case "SAVEFILEUPLOAD":
        $path = filter_input(INPUT_POST, "path", FILTER_SANITIZE_STRING);        
        $type = filter_input(INPUT_POST, "type", FILTER_SANITIZE_STRING);        
        $upl = new FileUpload();
        if($type === "/documents/") { $upl->valid_mimes = "pdf,doc,mp3,docx"; }
        $upl->destination = $path;
        $upl->upload();
        break;
    
    /* Delete file */
    case "REMOVEFILE":
        $root = filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING);
        $file = filter_input(INPUT_POST, "file", FILTER_SANITIZE_STRING);
        if(!empty($file)) {
            if(!unlink($root . $file)) {
                echo "Failed to remove file " . $this->strOldUrl;
            } else {
                echo TRUE;
            }
        }
    break;
    
    /* Returns form to edit filename */
    case "GETFILEEDITFORM":
        $file = filter_input(INPUT_POST, "file", FILTER_SANITIZE_STRING);
        $strHtml = "<form method=\"post\" id=\"fileform\" class=\"form-horizontal\">\n" . 
                    "<fieldset>\n" .
                    "<input type=\"hidden\" name=\"mode\" value=\"savefileedit\">" . 
                    "<input type=\"hidden\" name=\"oldfile\" value=\"".$file."\">" . 
                    "<div class=\"form-group\" data-group=\"file\">\n" .
                    "   <label class=\"col-sm-2 control-label for=\"file\">Filnavn:</label>\n" . 
                    "   <div class=\"col-sm-10\">\n" . 
                    "       <input type=\"text\" class=\"form-control\" name=\"file\" id=\"file\" value=\"".basename($file)."\" />\n" . 
                    "   </div>\n" . 
                    "</div>\n" .
                    "</fieldset>\n" .
                    "</form>\n";
        echo $strHtml;
    break;

    /* Save file with new filename */
    case "SAVEFILEEDIT":
        $root = filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . $path;
        $file = $root . filter_input(INPUT_POST, "file", FILTER_SANITIZE_STRING);
        $oldfile = $root . filter_input(INPUT_POST, "oldfile", FILTER_SANITIZE_STRING);
        rename($oldfile, $file);
        
        break;
    
    /* Returns folder form */
    case "GETFOLDERFORM":        
        $path = filter_input(INPUT_POST, "path", FILTER_SANITIZE_STRING);
        $action = filter_input(INPUT_POST, "action", FILTER_SANITIZE_STRING);
        $title = ($action === "new") ? "Opret folder" : "Rediger folder";
        $value = ($action === "new") ? "" : basename($path);
        $location = ($action === "new") ? $path : dirname($path) . "/";
        
        $strHtml = "<h2>" . $title . "</h2>\n" . 
                    "<form method=\"post\" id=\"folderform\" class=\"form-horizontal\">\n" . 
                    "<fieldset>\n" .
                    "<input type=\"hidden\" name=\"mode\" value=\"savefolder\">" . 
                    "<input type=\"hidden\" name=\"action\" value=\"".$action."\">" . 
                    "<div class=\"form-group\" data-group=\"file\">\n" .
                    "   <label class=\"col-sm-2 control-label for=\"foldername\">Folder navn:</label>\n" . 
                    "   <div class=\"col-sm-10\">\n" . 
                    "       <input type=\"text\" class=\"form-control\" name=\"foldername\" id=\"foldername\" value=\"".$value."\" />\n" . 
                    "   </div>\n" . 
                    "</div>\n" .
                    "<div class=\"form-group\" data-group=\"file\">\n" .
                    "   <div class=\"col-sm-10 col-offset-2\">Placering: " . $location ."</div>\n" . 
                    "</div>\n" .
                    "</fieldset>\n" .
                    "</form>\n";
        echo $strHtml;
    break;  

    /* Save folder */
    case "SAVEFOLDER":
        $action = filter_input(INPUT_POST, "action", FILTER_SANITIZE_STRING);
        $root = filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING);
        $path = filter_input(INPUT_POST, "path", FILTER_SANITIZE_STRING);
        $foldername = filter_input(INPUT_POST, "foldername", FILTER_SANITIZE_STRING);
        
        switch(strtoupper($action)) {
            case "NEW":
                if(!empty($foldername)) {
                    if(file_exists($root . $path)) {
                        if(!mkdir($root . $path . $foldername)) {
                            echo ERR_CREATEFOLDER;
                        }
                        echo $path . $foldername;
                    } else {
                        echo ERR_FOLDEREXIST;                
                    }
                }
                break;
            case "EDIT":
                $oldfolder = $root . $path;
                $newfolder = $root . dirname($path) . "/" . $foldername;
                if(!file_exists($newfolder)) {
                    rename($oldfolder, $newfolder);
                    echo dirname($path) . "/" . $foldername . "/";
                } else {
                    echo ERR_FOLDEREXIST;                
                }
                break;
        }
        break;   
    
    case "GETFOLDERDELETE":
        $path = filter_input(INPUT_POST, "path", FILTER_SANITIZE_STRING);
        
        $strHtml = "<h2>Slet folder</h2>\n" . 
                    "<form method=\"post\" id=\"folderform\" class=\"form-horizontal\">\n" . 
                    "<fieldset>\n" .
                    "<input type=\"hidden\" name=\"mode\" value=\"deletefolder\">" . 
                    "<input type=\"hidden\" name=\"mode\" value=\"".$path."\">" . 
                    "<div class=\"col-12\">Vil du slette folderen <i>" . basename($path) . "</i> og alt indhold?</div>\n" .
                    "</fieldset>\n" .
                    "</form>\n";
        echo $strHtml;
        
        break;
    
    case "DELETEFOLDER":
        $root = filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING);
        $path = filter_input(INPUT_POST, "path", FILTER_SANITIZE_STRING);
        if(!rmdir($root . $path)) {
            echo ERR_DELETEFOLDER;
        }
        echo dirname($path) . "/";
        break;
        
    case "GETFOLDERLIST":
        $dir = filter_input(INPUT_POST, "dir", FILTER_SANITIZE_STRING);
        $postDir = rawurldecode(DOCROOT.(!empty($dir) ? $dir : null));
        if(file_exists($postDir)) {
            $folders = array_diff(scandir($postDir), array(".",".."));
            $strHtml = "<ul class=\"foldertree\">\n";
            foreach($folders as $key => $value) {
                if(is_dir($postDir . $value)) {
                    $dirRel = $dir . $value;
                    $strHtml .= "<li class=\"directory collapsed\"><a rel=\"" . $dirRel . "/\">" . $value . 
                                    "</a></li>\n";
                }
            }
            $strHtml .= "</ul>\n";
            echo $strHtml;
        }        
        break;
}               
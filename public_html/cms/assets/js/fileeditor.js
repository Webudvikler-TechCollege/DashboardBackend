var path = "";

function initEditor() {
    top.path = $("#root").val();
    getFolderTree(path);
    getFileList(path);
    checkRootFolder(path);
    
    $(window).keydown(function (event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            return false;
        }
    });    
}

/* Editor Navigation Functions */

/* Builds the folder tree */
function getFolderTree(curpath) {
    $(".foldermenu").folderTree({
        dir: $("#root").val(), 
        script: '/cms/assets/fileeditor.php',
        curpath: curpath
    }).on("click", "a", function () {
        top.path = $(this).attr("rel");
        checkRootFolder(path);
        getFileList($(this).attr("rel"));
    });
}

/* Build a list of files */
function getFileList(path) {
    $.ajax({
        type: "POST",
        url: "/cms/assets/fileeditor.php",
        data: "mode=getfilelist&path=" + path,
        cache: false,
        success: function (result) {
            var data = $.parseJSON(result);
            var strHtml = "";
            $.each(data, function (key, value) {
                if($("#root").val() === "/images/") {
                    strHtml += "<label class=\"file imgfile\">" + 
                                "   <input type=\"radio\" name=\"file\" id=\"" + value.file + "\" value=\"" + value.file + "\">" + 
                                "   <div><img src=\"" + value.file + "\" /></div>" +
                                "   <div>" +
                                "        <i class=\"fa fa-remove text-danger\" title=\"Slet fil\" onclick=\"removeFile('" + value.file + "')\"></i>" +
                                "        <i class=\"fa fa-pencil text-danger\" title=\"Rediger fil\" onclick=\"prepareFileEdit('" + value.file + "')\"></i>" +
                                "        <i class=\"fa fa-eye text-danger\" title=\"Se fil\" onclick=\"viewFile('" + value.file + "')\"></i>" +
                                "   </div>\n" +
                                "</label>";
                } else {
                    var filename = value.file.split(/[\\/]/).pop();
                    var fileext = value.file.split(/[\.]/).pop();
                    strHtml += "<label class=\"file docfile\">" + 
                                "   <div>" +
                                "        <i class=\"fa fa-remove text-danger\" title=\"Slet fil\" onclick=\"removeFile('" + value.file + "')\"></i>" +
                                "        <i class=\"fa fa-pencil text-danger\" title=\"Rediger fil\" onclick=\"prepareFileEdit('" + value.file + "')\"></i>" +
                                "        <i class=\"fa fa-eye text-danger\" title=\"Se fil\" onclick=\"viewFile('" + value.file + "')\"></i>" +
                                "   </div>\n" +
                                "   <input type=\"radio\" name=\"file\" id=\"" + value.file + "\" value=\"" + value.file + "\">" + 
                                "   <div><img src=\"/cms/assets/images/foldertree/"+getFileIcon(fileext)+"\">" + filename + "</div>" +
                                "</label>";
                }
            });
            $("#fileview").html(strHtml);

            $("input[type=radio]").click( function() {
                if(!$(".modal-footer .btn-warning").prev().hasClass("btn-success")) {
                    $(".modal-footer .btn-warning").before(getBtn("btn-success", "selectFile()", "Vælg fil"));
                }
            });

            var strBtnPanel = getBtn("btn-warning", "selectNone()", "Vælg ingen") +
                    getCloseBtn();
            setModalFooter(strBtnPanel);
        }
    });
}

/* Select a file and return to calling field */
function selectFile() {
    $("[name='" + $("#target").val() + "']").val($("input[type=radio]:checked").val());
    $("[name='holder_" + $("#target").val() + "']").html($("input[type=radio]:checked").val());
    $("#fileeditor").modal("hide");    
}

/* View a selected file */
function viewFile(file) {
    $.ajax({
        type: "POST",
        url: "/cms/assets/fileeditor.php",
        data: "mode=fileview&file=" + file,
        cache: false,
        success: function (result) {
            $("#fileview").html(result);
        }
    });
    var strBtnPanel = getBtn("btn-default", "getFileList('" + path + "')", "Tilbage") +
            getCloseBtn();
    setModalFooter(strBtnPanel);
}

/* File Handle Functions */

/* Get upload form */
function prepareFileUpload() {
    $.ajax({
        type: "POST",
        url: "/cms/assets/fileeditor.php",
        data: "mode=fileuploadform",
        cache: false,
        success: function (result) {
            $("#fileview").html(result);
        }
    });
    var strBtnPanel = getBtn("btn-primary", "saveFileUpload()", "Gem") +
            getBtn("btn-default", "getFileList('" + path + "')", "Annuller") +
            getCloseBtn();
    setModalFooter(strBtnPanel);
}

/* Build list for multiple file upload */
function listFileUpload(input) {
    var arrFiles = [];
    $.merge(arrFiles, input.files);
    var strList = "<ol>";
    $.each(arrFiles, function (key, value) {
        strList += "<li>" + value.name + " (" + humanFileSize(value.size) + ")</li>\n";
    });
    strList += "</ol>";
    $("#uploadlist div").html(strList);
    $("#uploadlist label").html("Filer:");
}
;

/* Upload files to server */
function saveFileUpload() {
    var data = new FormData($("#uploadform")[0]);
    data.append("path", path);
    data.append("type", $("#root").val());

    $.ajax({
        type: "POST",
        contentType: false,
        processData: false,
        data: data,
        url: "/cms/assets/fileeditor.php",
        cache: false,
        success: function () {
            getFileList(path);
        }
    });
}

/* Get file edit form */
function prepareFileEdit(file) {
    $.ajax({
        type: "POST",
        url: "/cms/assets/fileeditor.php",
        data: "mode=getfileeditform&file=" + file,
        cache: false,
        success: function (result) {
            $("#fileview").html(result);
        }
    });
    var strBtnPanel = getBtn("btn-primary", "saveFileEdit()", "Gem") +
            getBtn("btn-default", "getFileList('" + path + "')", "Annuller") +
            getCloseBtn();
    setModalFooter(strBtnPanel);
}

/* Save file edit */
function saveFileEdit() {
    var data = new FormData($("#fileform")[0]);
    data.append("path", path);

    $.ajax({
        type: "POST",
        contentType: false,
        processData: false,
        data: data,
        url: "/cms/assets/fileeditor.php",
        cache: false,
        success: function (result) {
            getFolderTree(path);
            getFileList(path);
        }
    });
}

/* Delete file */
function removeFile(file) {
    $.ajax({
        type: "POST",
        url: "/cms/assets/fileeditor.php",
        data: "mode=removefile&file=" + file,
        cache: false,
        success: function () {
            getFileList(path);
        }
    });
}

/* Folder Handle Functions */

/* Get folder form */
function prepareFolderEdit(action) {
    $.ajax({
        type: "POST",
        url: "/cms/assets/fileeditor.php",
        data: "mode=getfolderform&path=" + path + "&action=" + action,
        cache: false,
        success: function (result) {
            $("#fileview").html(result);
        }
    });
    var strBtnPanel = getBtn("btn-primary", "saveFolderEdit()", "Gem") +
            getBtn("btn-default", "getFileList('" + path + "')", "Annuller") +
            getCloseBtn();
    setModalFooter(strBtnPanel);
}

/* Save folder */
function saveFolderEdit() {
    var data = new FormData($("#folderform")[0]);
    data.append("path", path);

    $.ajax({
        type: "POST",
        contentType: false,
        processData: false,
        data: data,
        url: "/cms/assets/fileeditor.php",
        cache: false,
        success: function (path) {
            getFolderTree(path);
            getFileList(path);
        }
    });
}

/* Get folder delete form */
function prepareFolderDelete() {
    $.ajax({
        type: "POST",
        url: "/cms/assets/fileeditor.php",
        data: "mode=getfolderdelete&path=" + path,
        cache: false,
        success: function (result) {
            $("#fileview").html(result);
        }
    });
    var strBtnPanel = getBtn("btn-danger", "deleteFolder()", "Slet") +
            getBtn("btn-default", "getFileList('" + path + "')", "Annuller") +
            getCloseBtn();
    setModalFooter(strBtnPanel);
}

/* Delete folder */
function deleteFolder() {
    $.ajax({
        type: "POST",
        url: "/cms/assets/fileeditor.php",
        data: "mode=deletefolder&path=" + path,
        cache: false,
        success: function (path) {
            getFolderTree(path);
            getFileList(path);
        }

    });
}

function selectNone() {
    $("[name='" + $("#target").val() + "']").val("");
    $("[name='holder_" + $("#target").val() + "']").html("");
    $("#fileeditor").modal("hide");    
}

function checkRootFolder(path) {
    var arr = path.split("/");
    arr = arr.filter(function (n) {
        return n != ""
    });
    if (arr.length < 2) {
        $(".fld-edit").hide();
    } else {
        $(".fld-edit").show();
    }
}

function getFileIcon(ext) {
    var icon = "file";
    switch(ext) {
        case "pdf":
            icon = ext;
            break;
    }
    return icon + ".png";
}

/* Set editor footer */
function setModalFooter(strBtns) {
    $(".modal-footer").html(strBtns);
}

/* Get button to close editor */
function getCloseBtn() {
    return "<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Luk</button>\n";
}

/* Get custom button */
function getBtn(strClass, strScript, strText) {
    return "<button type=\"button\" class=\"btn " + strClass + "\" onclick=\"" + strScript + "\">" + strText + "</button>\n";
}

(function($) {
    $.fn.folderTree = function(args) {
        var opts, data;
        $elm = $(this);
        defaults = {
          dir: '/',
          script: '/files/filetree',
          curpath: '/'
        };
        
        opts = $.extend( {}, $.fn.folderTree.defaults, args);
        
        $elm.find("ul").remove();
        $elm.append("<ul class=\"foldertree\"><li class=\"directory expanded\"><a rel=\"" + opts.dir + "\">" + opts.dir.replace(/\//g,"") + "</a></li></ul>");
        getData(opts.dir);
        
        $elm.on("click", "a", function() {
           getData(this.rel);
        });
        
        if(opts.curpath) {
            var arrpath = opts.curpath.substring(opts.dir.length-1).split("/");
            var triggerpath = opts.dir;
            $.each(arrpath, function(key,value) {
                if(value.length > 0) {
                    triggerpath += value + "/";
                    getData(triggerpath);
                }
            });
        }
        
        function getData(dir) {
            data = { dir: dir, mode: "getfolderlist" }
            $.ajax({
               url: opts.script,
               type: 'POST',
               dataType: 'HTML',
               data: data
            }).done( function(data) {
                 return showTree(data, dir);
            });
        }
        
        function showTree(data, rel) {
            if($.trim($elm.contents().length) < 2) {
                $elm.append(data);                
            } else {
                $elm.find("li").removeClass("selected");
                li = $('[rel="' + rel + '"]').parent();
                li.find("ul").remove();
                li.siblings("li").removeClass("expanded").removeClass("selected");
                li.siblings("li").find("ul").remove();
                li.removeClass("collapsed").addClass("expanded selected");
                li.append(data);
            }
        }
                
        return $elm;
    };
}(jQuery));
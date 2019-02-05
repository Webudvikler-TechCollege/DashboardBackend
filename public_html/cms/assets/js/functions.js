/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function() {
    
    /* Display body when loaded - to avoid graphic jumps */
    $('body').show();
    
    /* BS Tooltip for header icons */
    $('header button').tooltip();
    $('.fldtools a').tooltip();
    $('[type=password]').tooltip();
  
    /*
    var HelloButton = function (context) {
        var ui = $.summernote.ui;
  
        // create button
        var button = ui.button({
            contents: '<i class="fa fa-child"/> Hello',
            tooltip: 'hello',
            click: function () {
            // invoke insertText method with 'hello' on editor module.
                context.invoke('editor.insertText', 'hello');
            }
        });

        return button.render();   // return button as jquery object 
    }    
    */
   
    /* Summernote Settings */
    $('.texteditor').summernote({
        height: 300,
        lang: 'da-DK',
        toolbar: [
            // [groupName, [list of button]]
            ['mybutton', ['hello']],
            ['style', ['style']],
            ['font', ['bold','italic','underline','color','clear']],
            ['para', ['ol','ul','paragraph']],
            ['insert', ['link','picture','video']],
            ['table', ['table']],
            ['misc', ['fullscreen','codeview','help']]
        ]
     });
    
    $(".btn-file").click( function() {
        $("#fileeditor #target").val($(this).attr("id"));
        $("#fileeditor #root").val($(this).data("root"));
        $("#fileeditor").modal("show");
        initEditor();
    });

    $(".btn-topics").click( function() {
        $("#topicpicker #elmType").val($(this).data("type"));
        $("#topicpicker #elmId").val($(this).data("id"));
        $("#topicpicker").modal("show");
        initTopicPicker();
    });    
    
    if($("#menutree")) {
        getContextMenu();
    }
});

//Remove function for delete 
function remove(id, mode, text) {    
    mode = mode || "?mode=delete&id=" + id;
    console.log(mode);
    text = text || "Vil du slette denne record?";
    if(confirm(text)) {
        document.location.href = mode;
    }
}

// Go back 
function goback() {
    window.history.back();
}

// Call Url
function getUrl(strUrl) {
    document.location.href = strUrl;
}

function fieldreset(fieldId) {
    $("[data-group='"+fieldId+"']").removeClass("has-success has-feedback");
    $("[data-group='"+fieldId+"']").removeClass("has-error has-feedback");
    $("[data-group='"+fieldId+"'] .form-control-feedback").remove();
    $("[data-group='"+fieldId+"'] .error").remove();
}

function fieldsuccess(fieldId) {
    fieldreset(fieldId);
    var strHtml = "<span class=\"inputerr fa fa-check form-control-feedback\"></span>\n"; 
    $("[data-group='"+fieldId+"']").addClass("has-success has-feedback");
    $("#"+fieldId).after(strHtml);
}

/*
 * 
 */
function fielderror(fieldId,errMsg,fieldType) {
    fieldreset(fieldId);
    if(fieldType === "select") {
        var strHtml = "<span class=\"form-control-feedback\"></span>\n"; 
    } else {
        var strHtml = "<span class=\"inputerr fa fa-ban form-control-feedback\"></span>\n"; 
    }
    strHtml += "<i class=\"error\">" + errMsg + "</i>\n";
    $("[data-group='"+fieldId+"']").addClass("has-error has-feedback");
    $("#"+fieldId).after(strHtml);
    $("#"+fieldId).select();

    if(fieldType === "select") {
        $("#"+fieldId).change( function() {
            if($("#"+fieldId).val()) {
                fieldsuccess(fieldId);
                $("#"+fieldId).focusout( function() {
                    fieldreset(fieldId);
                });
            }
        });    
    } else {
        $("#"+fieldId).keyup( function() {
            if($("#"+fieldId).val()) {
                fieldsuccess(fieldId);
                $("#"+fieldId).focusout( function() {
                    fieldreset(fieldId);
                });
            }
        });    
    }
}

/*
 * 
 */
function getWebsafeStr(str) {
    var newstr = "";
    
    for (i = 0; i < str.length; i++) {
        switch(str.charCodeAt(i)) {
            case 197:
                newstr += 'AA';
            break;
            case 198:
                newstr += 'AE';
            break;
            case 216:
                newstr += 'OE';
            break;
            case 229:
                newstr += 'aa';
            break;
            case 230:
                newstr += 'ae';
            break;
            case 248:
                newstr += 'oe';
            break;
            case 230:
                newstr += "ae";
            break;
            default:
                newstr += str.charAt(i);
            break;
        }
    }     
    newstr = newstr.toLowerCase();
    newstr = newstr.replace(/[^a-z0-9]+/g, "-");
    return newstr;
}

/**
 * 
 * @param {type} size
 * @returns {String}
 */
function humanFileSize(size) {
    var i = Math.floor( Math.log(size) / Math.log(1024) );
    return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
};

/**
 * 
 * @returns {undefined}
 */
function getContextMenu() {
    $("#root a").on("contextmenu", function(e) {
        $("html").on("contextmenu", function() {
            return false;
        });
        $("#folderopts li:first-child a").attr("href","?mode="+$("#menutree").data("mode")+"edit&i"+$("#menutree").data("mode")+"ID=" + this.id);
        $("#folderopts li:nth-child(2) a").attr("href","?mode="+$("#menutree").data("mode")+"details&i"+$("#menutree").data("mode")+"ID=" + this.id);
        $("#folderopts li:nth-child(3) a").attr("href","?mode="+$("#menutree").data("mode")+"edit&iParentID=" + this.id);
        $("#folderopts li:last-child a").attr("onclick","remove("+this.id+",'?mode="+$("#menutree").data("mode")+"delete&id=" + this.id + "')");
        $("#folderopts").css({"top":e.clientY, "left":e.clientX});
        $("#folderopts").show();
        $("html").on("click", function(e) {
            if($(e.target).parent().attr("id") !== "#folderopts") {
                $("#folderopts").hide();
            }
        });
    });    
}
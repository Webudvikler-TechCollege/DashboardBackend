var arrSelectedTopics = [];
var elmType = "";
var elmId = "";

function initTopicPicker() {
   getDefaultGroupId();
   getSelected();
}

function showtopics(groupId) {
    elmType = $("#elmType").val();
    elmId = $("#elmId").val();  
    
    $.ajax({
        type: "POST",
        url: "/cms/assets/topicpicker.php",
        data: "mode=listtopics&groupId=" + groupId + "&elmType=" + elmType + "&elmId=" + elmId,
        cache: false,
        success: function (result) {
            var data = $.parseJSON(result);
            var strHtml = "";
            $.each(data, function (key, value) {
                var checked = (value.isChecked) ? "checked" : "";
                if(key === 0) {
                    strHtml += "<div class=\"col-md-6\">";
                }
                strHtml += "<div class=\"form-group\">" + 
                            "   <label for=\"" + value.topicId + "\">" + 
                            "       <input type=\"checkbox\" " + checked + " name=\"topics[]\" onclick=\"selectItem(this)\" id=\"" + value.topicId + "\" value=\"" + value.topicId + "\">&nbsp;&nbsp" + 
                                    value.topicName +  
                            "   </label>" + 
                            "</div>";
                if((key%10 === 9) && key > 0) {
                    strHtml += "</div>\n" + 
                                "<div class=\"col-md-6\">";
                }
            });
            strHtml += "</div>";
            $("#topicview").html(strHtml);
        }
    });  
    
    var strBtnPanel = getBtn("btn-success", "saveTopics()", "Gem emner") +
            getCloseBtn();
    setModalFooter(strBtnPanel);
    
}

function selectItem(obj) {
    if($(obj).is(":checked")) {
        arrSelectedTopics.push(parseInt($(obj).attr("id")));
    } else {
        arrSelectedTopics.splice(arrSelectedTopics.indexOf($(obj).attr("id"),1));
    }
    console.log(arrSelectedTopics);
}

function getDefaultGroupId() {
    $.ajax({
        type: "POST",
        url: "/cms/assets/topicpicker.php",
        data: "mode=getdefaultId",
        cache: false,
        success: function (defgroupId) {
            showtopics(defgroupId);            
        }
    });
}

function getSelected() {
    elmType = $("#elmType").val();
    elmId = $("#elmId").val();
    
    $.ajax({
        type: "POST",
        url: "/cms/assets/topicpicker.php",
        data: "mode=getselected&elmType=" + elmType + "&elmId=" + elmId,
        cache: false,
        success: function (result) {
            var data = $.parseJSON(result);
            $.each(data, function(key, value) {
                arrSelectedTopics.push(value);
            });
        }
    });
}

function saveTopics() {
    $.ajax({
        type: "POST",
        url: "/cms/assets/topicpicker.php",
        data: "mode=savetopics&elmType=" + elmType + "&elmId=" + elmId + "&arrtopics=" + arrSelectedTopics,
        cache: false,
        success: function() {
            arrSelectedTopics = [];
            $("#topicpicker").modal("hide");
            window.location.reload();
        }
    });
    
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
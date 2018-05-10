var customizerPage = true;
var editMyPage = false;

$(document).ready(function () {
    checkIfPageIsCustomizable();

    $.ajax({
        type: 'POST',
        url: myBasePath + "/src/App/Content/Ajax/MyAdminAjax.php",
        data: {m: "customizerThemeSessionSet", theme: theme_var},
        error: function (xhr, textStatus, errorThrown) {
            console.log('error');
        }
    });

    $("#previewFrame").attr("src", myBasePath + "/?customizerTHEME=" + theme_var);

    var previewFrameContainer = $("#previewFrameContainer");

    $("#displayMobile").click(function () {
        previewFrameContainer.removeClass("desktopScreen");
        previewFrameContainer.removeClass("tabletScreen");
        previewFrameContainer.addClass("mobileScreen");
    });

    $("#displayTablet").click(function () {
        previewFrameContainer.removeClass("desktopScreen");
        previewFrameContainer.removeClass("mobileScreen");
        previewFrameContainer.addClass("tabletScreen");
    });

    $("#displayDesktop").click(function () {
        previewFrameContainer.removeClass("desktopScreen");
        previewFrameContainer.removeClass("mobileScreen");
        previewFrameContainer.addClass("desktopScreen");
    });

    /* var typingTimer;
     var doneTypingInterval = 1000;

     $('#siteTitle').change(function(){
     $("#previewFrame").attr("src", "/?custom_tags&siteNAME=" + $("#siteTitle").val() + "&siteDESCRIPTION=" + $("#siteDescription").val());
     });


     $('#siteDescription').change(function(){
     $("#previewFrame").attr("src", "/?custom_tags&siteNAME=" + $("#siteTitle").val() + "&siteDESCRIPTION=" + $("#siteDescription").val());
     });*/

    window.setInterval(keepAliveSession, 10000);

    function keepAliveSession() {
        checkIfPageIsCustomizable();

        $.ajax({
            type: 'POST',
            url: myBasePath + "/src/App/Content/Ajax/MyAdminAjax.php",
            data: "m=customizerKeepSession",
            error: function (xhr, textStatus, errorThrown) {
                console.log('error');
            }
        });
    }

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            $.ajax({
                type: 'POST',
                url: myBasePath + "/src/App/Content/Ajax/MyAdminAjax.php",
                data: {m: "customizerThemeSessionUnset"},
                error: function (xhr, textStatus, errorThrown) {
                    console.log('error');
                }
            });
        } else {
            $.ajax({
                type: 'POST',
                url: myBasePath + "/src/App/Content/Ajax/MyAdminAjax.php",
                data: {m: "customizerThemeSessionSet", theme: theme_var},
                error: function (xhr, textStatus, errorThrown) {
                    console.log('error');
                }
            });
        }
    })

});

function checkIfPageIsCustomizable() {
    var frame = $("#previewFrame").contents();

    if (frame.find("#customizer").length > 0) {
        checkMyPage(frame.find("#customizerPageId").val());
    } else {
        if (!$("#li_mypage_editor").hasClass("hidden")) {
            $("#li_mypage_editor").toggleClass("hidden");
            $("#myPageId").val("");
            editMyPage = false;
        }
    }
}

function checkMyPage(id) {
    $.ajax({
        type: 'POST',
        url: myBasePath + "/src/App/Content/Ajax/MyAdminAjax.php",
        data: "m=checkMyPageExist&pageId=" + id,
        success: enableMyPageEdits,
        error: function (xhr, textStatus, errorThrown) {
            console.log('error');
        }
    });
}

function enableMyPageEdits(value) {
    if (value == "true") {
        /*$("#previewFrame").contents().find("#customizer").click(function () {
         $(this).attr('contenteditable','true');
         });*/
        /*

         var editor = new MediumEditor($("#previewFrame").contents().find("#customizer"));*/
        editMyPage = true;
        if ($("#li_mypage_editor").hasClass("hidden")) {
            $("#li_mypage_editor").toggleClass("hidden");
        }
        $("#myPageId").val($("#previewFrame").contents().find("#customizerPageId").val());
    }
}

function saveMyPage() {
    var content = $("#previewFrame").contents().find("#customizer").html();
    //console.log(content);
    $.ajax({
        type: 'POST',
        url: myBasePath + "/src/App/Content/Ajax/saveMyPage.php",
        data: {content: Base64.encode(content), pageId: $("#previewFrame").contents().find("#customizerPageId").val()},
        error: function (xhr, textStatus, errorThrown) {
            console.log('error');
        }
    });
}

function reloadCustomizer() {
    editMyPage = false;
    $("#previewFrame").attr("src", myBasePath + "/?" + Math.floor(Math.random() * 1000) + "&" + "customizerTHEME=" + theme_var);
}

function closeCustomizer() {
    var confirm_v = confirm(t_exit_now);
    if (confirm_v == true) {
        document.location.href = myBasePath + "/my-admin/theme_manager";
    }
}

function enableMyPageCustomizer() {
    checkIfPageIsCustomizable();
}


var ThemeContent = $('#previewFrame').contents();

function getElement(item) {
    if ($('#previewFrame').contents().find(item)) {
        return $('#previewFrame').contents().find(item);
    } else {
        return false;
    }
}
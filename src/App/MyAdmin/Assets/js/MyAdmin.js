function sendAjax(parameters, successCallback) {
    var url = myBasePath + "/src/App/Content/Ajax/MyAdminAjax.php";
    $.ajax({
        type: 'POST',
        url: url,
        data: parameters,
        success: successCallback,
        error: function (xhr, textStatus, errorThrown) {
            console.log('error');
        }
    });
}

function categorySuccess(param) {
    if (param == "duplicate") {
        var newCategoryName = $("#newCategoryName");
        newCategoryName.val("");
        newCategoryName.select();
    } else {
        var categorySelect = $("#categorySelect");
        categorySelect.empty();
        categorySelect.html(param);
        $("#category-add").toggleClass("hidden");
    }
}

$(document).ready(function () {
    $("#category-add-button").click(function () {
        $("#category-add").toggleClass("hidden");
    });

    $("#addNewCategoryName").click(function () {
        var newCategoryName = $("#newCategoryName");
        if (newCategoryName.val() == "") {
            newCategoryName.select();
        } else {
            sendAjax("m=newCategory&category=" + newCategoryName.val(), categorySuccess);
        }
    });

    $("#editPostStatusButton").click(function () {
        $("#postStatusEdit").toggleClass("hidden");
        $("#editPostStatusButton").toggleClass("hidden");
    });

    $("#cancelPostStatusButton").click(function () {
        $("#postStatusEdit").toggleClass("hidden");
        $("#editPostStatusButton").toggleClass("hidden");
    });

    $("#okPostStatusButton").click(function () {
        var statusSelect = $("#postSTATUSselect");
        var status = statusSelect.val();
        switch (status) {
            case "pending":
            case "draft":
            case "publish":
                $("#postSTATUS").val(status);
                $("#postSTATUSLabel").text(statusSelect.find(":selected").text());
                $("#postStatusEdit").toggleClass("hidden");
                $("#editPostStatusButton").toggleClass("hidden");
                break;
            default:
                statusSelect.select();
                break;
        }

    });

    $("#editPagePUBLICButton").click(function () {
        $("#pagePUBLICEdit").toggleClass("hidden");
        $("#editPagePUBLICButton").toggleClass("hidden");
    });

    $("#cancelPagePUBLICButton").click(function () {
        $("#pagePUBLICEdit").toggleClass("hidden");
        $("#editPagePUBLICButton").toggleClass("hidden");
    });

    $("#okPagePUBLICButton").click(function () {
        var statusSelect = $("#pagePUBLICselect");
        var status = statusSelect.val();
        switch (status) {
            case "1":
            case "0":
                $("#pagePUBLIC").val(status);
                $("#pagePUBLICLabel").text(statusSelect.find(":selected").text());
                $("#pagePUBLICEdit").toggleClass("hidden");
                $("#editPagePUBLICButton").toggleClass("hidden");
                break;
            default:
                statusSelect.select();
                break;
        }

    });

    $("#changeLayout1").click(function () {
        var rel = $(this).attr("rel");
        $('body').hide().fadeIn(1000);
        $('#adminStyle').remove();
        $('head').append('<link rel="stylesheet" href="' + rel + '" id="adminStyle" type="text/css" />');
        sendAjax("m=changeAdminColor&color=default", null);
    });

    $("#changeLayout2").click(function () {
        var rel = $(this).attr("rel");
        $('body').hide().fadeIn(1000);
        $('#adminStyle').remove();
        $('head').append('<link rel="stylesheet" href="' + rel + '" id="adminStyle" type="text/css" />');
        sendAjax("m=changeAdminColor&color=Light", null);
    });

    $("#editPostDateButton").click(function () {
        $("#postDateEdit").toggleClass("hidden");
        $("#editPostDateButton").toggleClass("hidden");
    });

    $("#cancelPostDateButton").click(function () {
        $("#postDateEdit").toggleClass("hidden");
        $("#editPostDateButton").toggleClass("hidden");
    });

    $("#okPostDateButton").click(function () {
        var postDateOriginal = $("#postDateOriginal").val();
        var postDateObject = new Date(postDateOriginal);
        var postDateY = $("#dateY").val();
        var postDateM = $("#dateM").val();
        var postDateD = $("#dateD").val();
        var postDateH = $("#dateH").val();
        var postDateI = $("#dateI").val();

        var dateRegex = /^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
        if(!dateRegex.test(postDateD + "-" + postDateM + "-" + postDateY + " " + postDateH + ":" + postDateI + ":00"))
        {
            $('.editDateForm > input, .editDateForm > select').addClass('errorDate');
            return;
        }

        $('.editDateForm > input, .editDateForm > select').removeClass('errorDate');

       if( postDateY != postDateObject.getFullYear() ||
           postDateM != (postDateObject.getMonth() + 1) ||
           postDateD != postDateObject.getDate() ||
           postDateH != postDateObject.getHours() ||
           postDateI != postDateObject.getMinutes())
       {
           $("#postDate").val(postDateD + "-" + postDateM + "-" + postDateY + " " + postDateH + ":" + postDateI + ":00");

           $("#postDateLabel").addClass("hidden");
           $("#postDatePlanned").removeClass("hidden");
           $("#postDatePlannedLabel").text(postDateD + "-" + postDateM + "-" + postDateY + " " + postDateH + ":" + postDateI + ":00");
       } else {
           $("#postDatePlanned").addClass("hidden");
           $("#postDateLabel").removeClass("hidden");
       }

        $("#postDateEdit").toggleClass("hidden");
        $("#editPostDateButton").toggleClass("hidden");
            //var date = new Date(postDateY + "-" + postDateM + "-" + postDateD + " " + postDateH + ":" + postDateI + ":00" )
    });

    function savePostEdit() {
        if($('#postId').val() != "")
        {
            sendAjax("m=savePostDraft&id=" + $('#postId').val() + "&title=" + $('#postTitle').val() + "&content=" + encodeURI(tinyMCE.get('postContent').getContent()), null)
        }
    }

    var postEditTimer;
    $('#postTitle').keyup(function(){
        clearTimeout(postEditTimer);
        if ($('#postTitle').val()) {
            postEditTimer = setTimeout(function () {
                savePostEdit()
            }, 2000);
        }
    });

    $('#postContent_ifr').contents().keyup(function(){
        clearTimeout(postEditTimer);
        if (tinyMCE.get('postContent').getContent()) {
            postEditTimer = setTimeout(function () {
                savePostEdit()
            }, 2000);
        }
    });

});

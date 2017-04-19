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

});

<?php
$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("upload_files"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

define('PAGE_ID', 'admin_upload_addons');
define('PAGE_NAME', $this->container['languages']->ta('page_upload', true));
define('MEDIA_ADDON_PAGE', true);
$this->getFileAdmin('header');

?>
<style>
    .libraryBar
    {
        width: 100%;
        background-color: #fff;
        padding: 8px;
    }

    .mediaFiles
    {
        list-style-type: none;
        padding: 2px;
    }

    .mediaFiles > .mediaFile
    {
        box-sizing: border-box;
        padding: 8px;
    }

    .mediaFile:focus
    {
        outline: 0;
        box-shadow: inset 0 0 2px 3px #f1f1f1, inset 0 0 0 7px #5b9dd9;
    }

    .mediaFile > .preview
    {
        padding: 4px;
        text-align: center;
        cursor: pointer;
        overflow: hidden;

        position: relative;
        max-height: 150px;
        min-height: 150px;
    }

    .centerPreview
    {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%,-50%);
    }
</style>
<div class="container" style="width: 100%">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="page-header h1PagesTitle"><?php $this->container['languages']->ta('page_upload_header'); ?></h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <ul class="nav nav-tabs">
                <li id="insertNewMediaMenu"><a data-toggle="tab" href="#insertMedia"><?php $this->container['languages']->ta('page_upload_new_header'); ?></a></li>
                <li class="active" id="addMediaMenu"><a data-toggle="tab" href="#addMedia"><?php $this->container['languages']->ta('page_upload_header'); ?></a></li>
            </ul>
            <div class="tab-content">
                <div id="insertMedia" class="tab-pane fade in">
                    <div class="panel b_panel">
                        <div class="panel-heading text-center"></div>
                        <div class="panel-body">
                            <div class="row">
                                <?php
                                    include MY_ADMIN_PATH . DIRECTORY_SEPARATOR . 'Pages' . DIRECTORY_SEPARATOR . 'upload_new.php';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="addMedia" class="tab-pane fade in active">
                    <div class="panel b_panel">
                        <div class="panel-heading text-center"></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="libraryBar b_panel">
                                        <div class="container" style="width: 100%;">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <select id="mediaTypeSelect" class="form-control b_form-control">
                                                        <option value="all"><?php $this->container['languages']->ta('page_upload_search_bar_option_all'); ?></option>
                                                        <option value="image"><?php $this->container['languages']->ta('page_upload_search_bar_option_images'); ?></option>
                                                        <option value="audio"><?php $this->container['languages']->ta('page_upload_search_bar_option_audio'); ?></option>
                                                        <option value="video"><?php $this->container['languages']->ta('page_upload_search_bar_option_videos'); ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <input type="text" id="searchMediaInput" class="form-control b_form-control" placeholder="<?php $this->container['languages']->ta('page_upload_search_bar_search_media'); ?>">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="container" style="width: 100%;">
                                        <ul id="sortableView" class="mediaFiles ui-sortable ui-sortable-disabled row">
                                        </ul>
                                        <h4 class="text-center hidden" id="mediaNotFound"><?php $this->container['languages']->ta('page_upload_search_bar_search_not_found'); ?></h4>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->getFileAdmin('footer'); ?>

<script>
    $( function() {

        if(typeof(parent.disableMediaType) != "undefined" && parent.disableMediaType !== null)
        {
            if(parent.disableMediaType)
            {
                $("#mediaTypeSelect").hide()
            }
        }

        var dataQuery = {
            mimeType: "",
            search: "",
            orderby: "date",
            order: "desc"
        }

        if(typeof(parent.mediaAddonDataQuery) != "undefined" && parent.mediaAddonDataQuery !== null)
        {
            dataQuery = parent.mediaAddonDataQuery;
        }

        var writeTimer;
        $('#searchMediaInput').on('keyup', function () {
            clearTimeout(writeTimer);
            dataQuery["mimeType"] = $('#mediaTypeSelect').value;
            dataQuery["search"] = $('#searchMediaInput').val();
            writeTimer = setTimeout(searchMedia, 1000);
        });

        $("#addMediaMenu").on('click', function () {
            dataQuery["search"] = $('#searchMediaInput').val();
            searchMedia()
        });

        $('#searchMediaInput').on('keydown', function () {
            clearTimeout(writeTimer);
        });

        $('#mediaTypeSelect').on('change', function () {
            dataQuery["mimeType"] = this.value;
            dataQuery["search"] = $('#searchMediaInput').val();
            searchMedia()
        });

        var searchMediaData = null;

        function searchMedia() {
            sendAjax(jQuery.param({
                action: "query-media",
                data: dataQuery
            }), function (data) {
                searchMediaData = data
                orderMedia()
            });
        }

        searchMedia();

        function orderMedia()
        {
            $("#sortableView").text("")
            $("#mediaNotFound").addClass("hidden")

            if($.isEmptyObject(searchMediaData))
            {
                $("#mediaNotFound").removeClass("hidden")
                return;
            }
            $.each(searchMediaData, function (i, element) {

                var image = '<i class="fa fa-file fa-4x"></i>'

                if($.inArray(/(?:\.([^.]+))?$/.exec(element.name.toLowerCase())[1], ['jpg', 'jpeg', 'jpe', 'gif', 'png']) >= 0)
                {
                    var date = new Date(element.date)
                    var url = '{@siteURL@}/src/App/Content/Storage/Upload/' + date.getFullYear() + '/' + ("0" + (parseInt(date.getMonth()) + 1)).slice(-2) + '/' + element.name;

                    image = '<img src="'+ url +'" />';
                }

                if($.inArray(/(?:\.([^.]+))?$/.exec(element.name.toLowerCase())[1], ['mp3', 'ogg', 'm4a', 'wav']) >= 0)
                {
                    image = '<i class="fa fa-file-audio-o fa-4x"></i>';
                }


                if($.inArray(/(?:\.([^.]+))?$/.exec(element.name.toLowerCase())[1], ['mp4', 'm4v', 'webm', 'ogv', 'flv']) >= 0)
                {
                    image = '<i class="fa fa-file-video-o fa-4x"></i>';
                }

                $("#sortableView").append(`
            <li class="mediaFile col-lg-3 col-md-3 col-sm-4 col-xs-6" data-id="${i}">
                <div class="preview b_panel">
                    <div class="centerPreview">
                    ${image}
                    <br>
                    <small>${element.title}</small>
                    </div>
                </div>
            </li>`)

            })

            $(".mediaFile").on('click', function ()
            {
                var date = new Date(searchMediaData[$(this).data('id')].date)
                var url = '{@siteURL@}/src/App/Content/Storage/Upload/' + date.getFullYear() + '/' + ("0" + (parseInt(date.getMonth()) + 1)).slice(-2) + '/' + searchMediaData[$(this).data('id')].name;

                if(typeof(parent.tmpMediaAddonUrl) != "undefined" && parent.tmpMediaAddonUrl !== null)
                {
                    parent.tmpMediaAddonUrl = url
                }

                if(typeof(parent.tmpMediaAddonFunctionCallBack) != "undefined" && parent.tmpMediaAddonFunctionCallBack !== null)
                {
                    parent.tmpMediaAddonFunctionCallBack();
                }

                if(typeof(parent.tinymce) != "undefined" && parent.tinymce !== null) {
                    if ($.inArray(/(?:\.([^.]+))?$/.exec(searchMediaData[$(this).data('id')].name.toLowerCase())[1], ['jpg', 'jpeg', 'jpe', 'gif', 'png']) >= 0) {
                        parent.tinymce.activeEditor.execCommand('mceInsertContent', false, '<img src="' + url + '" alt="' + searchMediaData[$(this).data('id')].caption + '" />');
                        return;
                    }

                    if ($.inArray(/(?:\.([^.]+))?$/.exec(searchMediaData[$(this).data('id')].name.toLowerCase())[1], ['mp4', 'm4v', 'webm', 'ogv', 'flv']) >= 0) {
                        parent.tinymce.activeEditor.execCommand('mceInsertContent', false, '<video controls="controls"><source src="' + url + '" type="' + searchMediaData[$(this).data('id')].mime_type + '" /></video>');
                        return;
                    }

                    if ($.inArray(/(?:\.([^.]+))?$/.exec(searchMediaData[$(this).data('id')].name.toLowerCase())[1], ['mp3', 'ogg', 'm4a', 'wav']) >= 0) {
                        parent.tinymce.activeEditor.execCommand('mceInsertContent', false, '<audio src="' + url + '" controls="controls"></audio>');
                        return;
                    }

                    parent.tinymce.activeEditor.execCommand('mceInsertContent', false, '<a href="' + url + '">' + searchMediaData[$(this).data('id')].title + '</a>');
                }
            })
        }

    } );
</script>

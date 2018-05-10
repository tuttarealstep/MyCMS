<?php
$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("upload_files"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}
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
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ta('page_upload_header'); ?>
                <a href="{@siteURL@}/my-admin/upload_new"
                   class="btn btn-primary pull-right"><?php $this->container['languages']->ta('page_upload_media_add_new_button'); ?></a>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
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

<div id="infoModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <label><i class="fa fa-calendar"></i> <?php $this->container['languages']->ta('page_upload_edit_media_mediaPostDate'); ?> <b id="fileDateB"></b></label><br>
                <label><?php $this->container['languages']->ta('page_upload_edit_media_mediaFileName'); ?> <b id="fileNameB"></b></label><br>
                <label><?php $this->container['languages']->ta('page_upload_edit_media_mediaFileType'); ?> <b id="fileTypeB"></b></label><br>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" id="modalUrlButton"><?php $this->container['languages']->ta('page_upload_moreInfoEdit'); ?></a>
            </div>
        </div>
    </div>
</div>

<?php $this->getFileAdmin('footer'); ?>
<script>
    $( function() {
       /* $( "#sortableView" ).sortable();
        $( "#sortableView" ).disableSelection();*/

        var dataQuery = {
            mimeType: "",
            search: "",
            orderby: "date",
            order: "desc"
        }

        var writeTimer;
        $('#searchMediaInput').on('keyup', function () {
            clearTimeout(writeTimer);
            dataQuery["mimeType"] = $('#mediaTypeSelect').value;
            dataQuery["search"] = $('#searchMediaInput').val();
            writeTimer = setTimeout(searchMedia, 1800);
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

                if($.inArray(/(?:\.([^.]+))?$/.exec(element.name)[1], ['jpg', 'jpeg', 'jpe', 'gif', 'png']) >= 0)
                {
                    var date = new Date(element.date)
                    var url = '{@siteURL@}/src/App/Content/Storage/Upload/' + date.getFullYear() + '/' + ("0" + (parseInt(date.getMonth()) + 1)).slice(-2) + '/' + element.name;

                    image = '<img src="'+ url +'" />';
                }

                if($.inArray(/(?:\.([^.]+))?$/.exec(element.name)[1], ['mp3', 'ogg', 'm4a', 'wav']) >= 0)
                {
                    image = '<i class="fa fa-file-audio-o fa-4x"></i>';
                }


                if($.inArray(/(?:\.([^.]+))?$/.exec(element.name)[1], ['mp4', 'm4v', 'webm', 'ogv', 'flv']) >= 0)
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

            $(".mediaFile").on('click', function () {
                $(".modal-title").text(searchMediaData[$(this).data('id')].title)
                $("#fileDateB").text(searchMediaData[$(this).data('id')].date)
                $("#fileTypeB").text(searchMediaData[$(this).data('id')].mime_type)
                $("#fileNameB").text(searchMediaData[$(this).data('id')].name)
                $("#modalUrlButton").attr("href", "{@siteURL@}/my-admin/upload?id=" + searchMediaData[$(this).data('id')].id + "&action=edit")
                $('#infoModal').modal('show');
            })
        }
        
    } );
</script>


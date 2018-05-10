<?php

$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("upload_files"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

if(!defined("MEDIA_ADDON_PAGE")) {
    define('PAGE_ID', 'admin_upload');
    define('PAGE_NAME', $this->container['languages']->ta('page_upload', true));
}

$this->getFileAdmin('header');

if(!defined("MEDIA_ADDON_PAGE"))
{
    $this->getPageAdmin('topbar');
}
?>
<style>
    .mediaUploadCircle
    {
        border: 2px dashed #9e9e9e;
        padding: 5px;
        border-radius: 20px;
        height: 40vh;
    }

    .mediaUploadCircle.borderColor
    {
        border-color: #3F51B5;
    }

    .mediaUploadCircle > .inside
    {
        position: absolute;
        left: 50%;
        top: 50%;
        -webkit-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
    }

    #containerUploadInfo > div.infoDiv
    {
        height: 35px;
        line-height: 35px;
        border-bottom: 2px solid #2196f3;
    }
    #containerUploadInfo > div.infoDiv.error
    {
        border-bottom: 2px solid #C62828;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ta('page_upload_new_header'); ?>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <form enctype="multipart/form-data" method="post" id="uploadFileForm">
            <div class="mediaUploadCircle">
                    <div class="inside">
                        <input type="file" name="files[]" id="fileInputButton" multiple="multiple" style="display:none">
                        <span><h2><?php $this->container['languages']->ta('page_upload_new_drag_dropFile'); ?></h2></span>
                        <small><span><?php $this->container['languages']->ta('page_upload_new_or_select'); ?></span>
                        <a href="#" name="selectFile" id="selectFile" ><i><?php $this->container['languages']->ta('page_upload_new_select_button'); ?></i></a>
                        </small>
                    </div>
            </div>
    </form>
        </div>
        <div class="col-lg-12" id="containerUploadInfo">
            <br>
            <p><?php echo $this->container['languages']->ta("upload_max_file_dimension_label"); ?> <?php echo \MyCMS\App\Utils\Management\MyCMSFileManager::getMaximumFileUploadSize(true) ?></p>
            <hr>
        </div>
    </div>
</div>
<?php $this->getFileAdmin('footer'); ?>
<script>
    <?php
    if(defined("MEDIA_ADDON_PAGE"))
    {
        ?>
            var MEDIA_ADDON_PAGE = true;
        <?php
    }
    ?>
    $('#selectFile').click(function() {
        $('#fileInputButton').click();
    });

    $('#uploadFileForm').on(
        'dragover dragenter',
        function(event) {
            event.preventDefault();
            event.stopPropagation();

            $('.mediaUploadCircle').addClass("borderColor")
        }
    )

    $('#uploadFileForm').on('dragleave', function () {
        $('.mediaUploadCircle').removeClass("borderColor")
    })

    $('#uploadFileForm').on(
        'drop',
        function(event){
            $('.mediaUploadCircle').removeClass("borderColor")
            if(event.originalEvent.dataTransfer){
                if(event.originalEvent.dataTransfer.files.length) {
                    event.preventDefault();
                    event.stopPropagation();
                    uploadMedia(event.originalEvent.dataTransfer.files)
                }
            }
        }
    );

    $('#fileInputButton').on('change', function() {
        uploadMedia(this.files)
    });

    function removeElement(element) {
        $(element).parent().remove()
    }


</script>

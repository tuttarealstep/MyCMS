<?php
$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("upload_files"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

if(isset($_POST['mediaUpdate']))
{
    $mediaTitle = $_POST['mediaTitle'];
    $mediaCaption = $_POST['mediaCaption'];
    $mediaDescription = $_POST['mediaDescription'];
    $dateEdit = date('Y-m-d H:i:s', time());
    $this->container['database']->query("UPDATE my_media SET title = :title, description = :description, caption = :caption, date = :date WHERE id = :id", [
            "title" => $mediaTitle,
            "caption" => $mediaCaption,
            "description" => $mediaDescription,
            "date" => $dateEdit,
        "id" => (int)$_GET['id']
    ]);
}

if(isset($_GET['id']))
{
    $result = $this->container['media']->getMedia((int)$_GET['id']);
    if(!$result)
        header("location: /my-admin/home");

    $result = (object)$result;
    $mediaId = $result->id;
    $title = $result->title;
    $description = $result->description;
    $date = date('Y-m-d H:i:s', strtotime($result->date));
    $name = $result->name;
    $caption = $result->caption;
    $mimeType = $result->mime_type;
    $mediaUrl = "{@siteURL@}/src/App/Content/Storage/Upload/" . date('Y', strtotime($result->date)) . "/" . date('m', strtotime($result->date)) . "/" . $result->name;

    $filePath = C_PATH . DIRECTORY_SEPARATOR . "Storage" . DIRECTORY_SEPARATOR . "Upload" . DIRECTORY_SEPARATOR . date('Y', strtotime($result->date)) . DIRECTORY_SEPARATOR . date('m', strtotime($result->date))  . DIRECTORY_SEPARATOR . $result->name;

    if(!file_exists($filePath))
        header("location: /my-admin/home");

    $fileSize = $this->container['functions']->fileSizeConvert(filesize($filePath));

}
?>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="h1PagesTitle"><?php $this->container['languages']->ea('page_upload_edit_media_header'); ?>
                    <a href="{@siteURL@}/my-admin/upload_new"
                       class="btn btn-primary pull-right"><?php $this->container['languages']->ta('page_upload_edit_media_add_new_button'); ?></a>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <form role="form" method="post" action="">
                    <div class="row">
                        <div class="col-lg-8 col-md-8">
                            <div class="panel b_panel">
                                <div class="panel-body b_panel-body panel-body-padding">
                                    <div class="form-group">
                                        <input type="text"
                                               placeholder="<?php $this->container['languages']->ta('page_upload_edit_media_title_input'); ?>"
                                               name="mediaTitle"
                                               id="mediaTitle" class="form-control b_form-control" maxlength="200"
                                               value="<?php echo $title; ?>"
                                        >
                                    </div>
                                    <br/>
                                    <div class="form-group">
                                        <?php
                                            if($this->container['media']->isMediaImage($mediaId))
                                            {
                                                ?>
                                                <img src="<?php echo $mediaUrl; ?>" style="max-width: 100%"/>
                                                <?php
                                            } else {
                                                ?>
                                                <i class="fa fa-file fa-4x"></i>
                                                <?php
                                            }
                                        ?>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label><?php $this->container['languages']->ta('page_upload_edit_media_caption_input'); ?></label>
                                        <textarea name="mediaCaption" class="form-control" placeholder="<?php $this->container['languages']->ta('page_upload_edit_media_caption_input'); ?>"><?php echo $caption; ?></textarea>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label><?php $this->container['languages']->ta('page_upload_edit_media_description_input'); ?></label>
                                        <textarea name="mediaDescription" class="form-control" placeholder="<?php $this->container['languages']->ta('page_upload_edit_media_description_input'); ?>"><?php echo $description; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col-lg-8 -->
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <div class="panel b_panel">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                          <?php $this->container['languages']->ta('page_upload_edit_media_save_panel'); ?>
                                        </h4>
                                    </div>
                                        <div class="panel-body b_panel-body">
                                            <div class="panel-body-padding">
                                                <div class="form-group">
                                                    <label for="mediaUrl"><?php $this->container['languages']->ta('page_upload_edit_media_mediaUrlLabel'); ?></label>
                                                    <input type="text" id="mediaUrl" readonly class="form-control b_form-control" value="<?php echo $mediaUrl; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label><i class="fa fa-calendar"></i> <?php $this->container['languages']->ta('page_upload_edit_media_mediaPostDate'); ?> <b><?php echo $date; ?></b></label>
                                                </div>
                                                <div class="form-group">
                                                    <label><?php $this->container['languages']->ta('page_upload_edit_media_mediaFileName'); ?> <b><?php echo $name; ?></b></label>
                                                </div>
                                                <div class="form-group">
                                                    <label><?php $this->container['languages']->ta('page_upload_edit_media_mediaFileType'); ?> <b><?php echo $mimeType; ?></b></label>
                                                </div>
                                                <div class="form-group">
                                                    <label><?php $this->container['languages']->ta('page_upload_edit_media_mediaFileSize'); ?> <b><?php echo $fileSize; ?></b></label>
                                                </div>
                                                <div class="form-group">
                                                    <small><a style="color: #B71C1C; text-decoration: underline;" href="{@siteURL@}/my-admin/upload?id=<?php echo $mediaId; ?>&action=delete"><?php $this->container['languages']->ta('page_upload_edit_media_mediaDelete'); ?></a></small>
                                                </div>
                                            </div>
                                        </div>
                                    <button type="submit" name="mediaUpdate" id="mediaUpdate"
                                            class="btn btn-primary b_btn btn-block"><?php $this->container['languages']->ta('page_upload_edit_media_updateButton'); ?></button>

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php $this->getFileAdmin('footer'); ?>
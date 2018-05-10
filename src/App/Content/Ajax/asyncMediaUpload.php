<?php
define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);
include '../../../../src/Bootstrap.php';

if(!$app->container['users']->currentUserHasPermission("upload_files"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

if ($app->container['users']->userLoggedIn()) {
    $destinationFolder = C_PATH . DIRECTORY_SEPARATOR . "Storage" . DIRECTORY_SEPARATOR . "Upload";
    for ($i = 0; $i < count($_FILES); $i++) {
        $fileManager = new \MyCMS\App\Utils\Management\MyCMSFileManager($_FILES[ $i ]);
        if ($fileManager::checkFileType($fileManager->name, $app->container['plugins']->applyEvent('mimeTypes'))['ext'] == false) {
            echo "<div class='infoDiv error'><b>" . $fileManager->name . "</b>: " . $app->container['languages']->ta('upload_error_file_not_supported', true) . " <a href='#' style='float: right' onclick='removeElement(this)'>x</a></div>";
            continue;
        }
        if (!$fileManager->move(true, $destinationFolder, $fileManager->name, true)) {
            switch ($fileManager->getInfo()) {
                case 'permission denied':
                    echo "<div class='infoDiv error'><b>" . $fileManager->name . "</b>: " . $app->container['languages']->ta('upload_permission_denied', true) . " <a href='#' style='float: right' onclick='removeElement(this)'>x</a></div>";
                    break;
                case 'maximum file upload size':
                    echo "<div class='infoDiv error'><b>" . $fileManager->name . "</b>: " . $app->container['languages']->ta('upload_maximum_file_upload_size', true) . " <a href='#' style='float: right' onclick='removeElement(this)'>x</a></div>";
                    break;
            }
            continue;
        }

        $title = $fileManager->getInfo();
        $description = "";
        $date = date('Y-m-d H:i:s', time());
        $author = $_SESSION['user']['id'];
        $name = $fileManager->getInfo();/*substr($app->container['blog']->generateUniqueName($title, rand()), 0, 200);*/
        $caption = "";
        $mimeType = $fileManager->type;

        $app->container['database']->query("INSERT INTO my_media VALUES (
NULL,
:title,
:description,
:date,
:dateEdit,
:author,
:name,
:caption,
:mime_type
)", [
            "title"       => $title,
            "description" => $description,
            "date"        => $date,
            "dateEdit"    => $date,
            "author"      => $author,
            "name"        => $name,
            "caption"     => $caption,
            "mime_type"   => $mimeType]);

        $mediaId = $app->container['database']->lastInsertId();

        echo "<div class='infoDiv'><b>" . $fileManager->name . "</b>: " . $app->container['languages']->ta('upload_successful_error', true) . " <a id='editMediaLink' href='/my-admin/upload?id=" . $mediaId . "&action=edit' style='float: right'>" . $app->container['languages']->ta('upload_edit_label', true) . "</a></div>";

        /*
                $newFileName = $fileManager->getInfo();
                echo $fileManager->getInfo().'<br/>';
                echo $fileManager->name.'<br/>';*/
    }

}

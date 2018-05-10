<?php

$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("upload_files"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

define('PAGE_ID', 'admin_upload');
define('PAGE_NAME', $this->container['languages']->ta('page_upload', true));

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');

$action = isset($_GET['action']) ? $_GET['action'] : "";
switch ($action)
{
    case 'edit':
        if(isset($_GET['id']))
        {
            include 'upload/editMedia.php';
        }
        break;
    case 'delete':
        if(isset($_GET['id']))
        {
            $result = $this->container['media']->getMedia((int)$_GET['id']);
            if(!$result)
                header("location: /my-admin/home");
            $result = (object)$result;

            $mediaPath = C_PATH . DIRECTORY_SEPARATOR . "Storage" . DIRECTORY_SEPARATOR . "Upload" . DIRECTORY_SEPARATOR . date('Y', strtotime($result->date)) . DIRECTORY_SEPARATOR . date('m', strtotime($result->date))  . DIRECTORY_SEPARATOR . $result->name;

            if(file_exists($mediaPath))
            {
                unlink($mediaPath);
            }

            $this->container['database']->query("DELETE FROM my_media WHERE id = :mediaId", ['mediaId' => (int)$_GET['id']]);

            header("location: /my-admin/upload");
            break;
        }
        break;
    default:
        include 'upload/showLibrary.php';
        break;
}


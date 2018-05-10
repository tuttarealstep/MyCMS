<?php

namespace MyCMS\App\Utils\Media;

use MyCMS\App\Utils\Models\Container;

class MyCMSMedia extends Container
{
    function __construct($container)
    {
        parent::__construct($container);
    }

    /**
     * @param $id
     * @return bool|array
     */
    function getMedia($id)
    {
        $result = $this->container['database']->row("SELECT * FROM my_media WHERE id = :mediaId", ['mediaId' => $id]);
        if(!$result)
            return false;

        return $result;
    }

    /**
     * Shortcut for check image media
     * @param $id
     * @return bool
     */
    function isMediaImage($id)
    {
        return $this->isMedia("image", $id);
    }

    /**
     * Check file type
     * @param $type
     * @param $id
     * @return bool
     */
    function isMedia($type, $id)
    {
        $media = (object)$this->getMedia($id);
        $extension = pathinfo($media->name, PATHINFO_EXTENSION);
        switch ($type)
        {
            case 'image':
                $imageExtension = ['jpg', 'jpeg', 'jpe', 'gif', 'png'];
                return in_array($extension, $imageExtension);
                break;
            case 'video':
                $videoExtension = ['mp4', 'm4v', 'webm', 'ogv', 'flv'];
                return in_array($extension, $videoExtension);
                break;
            case 'audio':
                $videoExtension = ['mp3', 'ogg', 'm4a', 'wav'];
                return in_array($extension, $videoExtension);
                break;
            default:
                //todo add other type
                return false;
                break;
        }
    }

    private function addMediaButton()
    {
        $mediaButton = function ()
        {
            ?>
            <a id="addMediaButton" class="default-addon" data-featherlight="iframe"
               href="{@siteURL@}/my-admin/upload/mediaAddon"><?php $this->container['languages']->ta('upload_addon_menu') ?></a>
            <?php
        };

        $this->container['plugins']->addEvent('myPageAddonsMenu', $mediaButton);
        $this->container['plugins']->addEvent('blogAddonsMenu',  $mediaButton);
    }

    function setMediaEvents()
    {
        $this->addMediaButton();
    }
}
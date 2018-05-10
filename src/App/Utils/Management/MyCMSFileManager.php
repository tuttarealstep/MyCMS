<?php

namespace MyCMS\App\Utils\Management;

use ZipArchive;

/**
 * Class MyCMSFileManager
 *
 * This class is in development, now only used for update and manage themes.
 * @package MyCMS\App\Utils\Management
 */
class MyCMSFileManager extends MyCMSContainer
{
    private $fileInfo = [];
    private $info;

    /**
     * MyCMSFileManager constructor.
     * @param $fileInfo
     */
    function __construct($fileInfo)
    {
        $this->fileInfo = $fileInfo;
    }

    /**
     * Move a uploaded file to a destination
     *
     * @param bool $isUploadFile
     * @param null | string $destination
     * @param null | string $customName
     * @param bool $archive
     * @return bool
     */
    public function move($isUploadFile = false, $destination = null, $customName = null, $archive = false)
    {
        $destination = (!empty($destination)) ? $destination : ((isset($this->fileInfo['destination'])) ? $this->fileInfo['destination'] : null);

        if ($destination == null) {
            return false;
        }

        $fileName = isset($this->fileInfo['file']) ? basename($this->fileInfo['file']) : (isset($this->fileInfo['tmp_name']) ? $this->fileInfo['tmp_name'] : null);

        if ($fileName == null)
            return false;

        if ($isUploadFile) {
            if (!is_writable($destination)) {
                $this->info = 'permission denied';

                return false;
            }

            if ($this->fileInfo['size'] > self::getMaximumFileUploadSize()) {
                $this->info = 'maximum file upload size';

                return false;
            }

            if ($customName == null) {
                $customName = $fileName;
            }

            $destination = $destination . DIRECTORY_SEPARATOR;

            if ($archive) {
                $archivePath = $destination . date('Y', time()) . DIRECTORY_SEPARATOR . date('m', time()) . DIRECTORY_SEPARATOR;
                if (!file_exists($archivePath)) {
                    mkdir($archivePath, 0755, true);
                }

                $destination = $archivePath;
            }

            if (file_exists($destination . $customName)) {
                $customName = self::getUniqueName($destination, $customName);
            }

            $customName = str_replace(" ", "_", $customName);

            $this->info = $customName;

            return move_uploaded_file($fileName, $destination . $customName);
        } else {
            return rename($fileName, $destination);
        }
    }

    /**
     * Extract a zip archive to a destination.
     *
     * @param $filePath
     * @param null $destination
     * @return bool|string
     */
    public function extract($filePath, $destination = null)
    {
        $destination = (!empty($destination)) ? $destination : ((isset($this->fileInfo['destination'])) ? $this->fileInfo['destination'] : null);

        $zip_extract = new ZipArchive;

        if ($zip_extract->open($filePath) === true) {
            $zip_extract->extractTo($destination);
            $zip_extract->close();

            return true;
        } else {
            return 'Error ZIP - <b>Can\'t Open, check Extension!</b>';
        }
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    function __get($name)
    {
        return $this->fileInfo[ $name ] ?: null;
    }

    static public function getUniqueName($path, $name)
    {
        $name = str_replace(" ", "_", mb_eregi_replace('\s+', ' ', $name));

        $i = 0;
        $originalName = pathinfo($name, PATHINFO_FILENAME);
        $uniqueName = pathinfo($name, PATHINFO_FILENAME);
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        while (file_exists("{$path}/{$uniqueName}.{$extension}")) {
            $i++;
            $uniqueName = $originalName . $i;
        }

        return $uniqueName . "." . $extension;
    }

    /**
     * This function returns the maximum files size that can be uploaded
     * @param bool $string
     * @return int
     */
    static function getMaximumFileUploadSize($string = false)
    {
        if ($string) {
            if (self::convertPHPSizeToBytes(ini_get('post_max_size')) > self::convertPHPSizeToBytes(ini_get('upload_max_filesize'))) {
                return ini_get('upload_max_filesize');
            } else {
                return ini_get('post_max_size');
            }
        } else {
            return min(self::convertPHPSizeToBytes(ini_get('post_max_size')), self::convertPHPSizeToBytes(ini_get('upload_max_filesize')));
        }
    }

    /**
     * This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
     *
     * @param string $stringSize
     * @return int The value in bytes
     */
    static function convertPHPSizeToBytes($stringSize)
    {
        //
        $stringSuffix = strtoupper(substr($stringSize, -1));
        if (!in_array($stringSuffix, ['P', 'T', 'G', 'M', 'K'])) {
            return (int)$stringSize;
        }
        $intValue = substr($stringSize, 0, -1);
        switch ($stringSuffix) {
            case 'P':
                $intValue *= 1024;
            case 'T':
                $intValue *= 1024;
            case 'G':
                $intValue *= 1024;
            case 'M':
                $intValue *= 1024;
            case 'K':
                $intValue *= 1024;
                break;
        }

        return (int)$intValue;
    }

    /**
     * @param $fileName
     * @param null $mimes
     * @return array
     */
    static public function checkFileType($fileName, $mimes)
    {
        $type = false;
        $ext = false;

        foreach ($mimes as $extensionPreg => $mime_match) {
            if (preg_match('!\.(' . $extensionPreg . ')$!i', $fileName, $extensionMatches)) {
                $type = $mime_match;
                $ext = $extensionMatches[1];
                break;
            }
        }

        return compact('ext', 'type');
    }
}
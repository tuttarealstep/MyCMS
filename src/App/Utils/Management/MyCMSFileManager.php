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
         * @param null $destination
         * @return bool
         */
        public function move($isUploadFile = false, $destination = null)
        {
            $destination = (!empty($destination)) ? $destination : ((isset($this->fileInfo['destination'])) ? $this->fileInfo['destination'] : null);

            if ($destination == null) {
                return false;
            }

            if ($isUploadFile) {
                return move_uploaded_file($this->fileInfo['file'], $destination . "/" . basename($this->fileInfo['file']));
            } else {
                return rename($this->fileInfo['file'], $destination);
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

    }
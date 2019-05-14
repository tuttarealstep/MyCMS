<?php
/**
 * User: tuttarealstep
 * Date: 10/04/16
 * Time: 0.08
 */

namespace MyCMS\App\Utils\Security;

class MyCMSSecurity
{
    private $settings;

    function __construct($settings)
    {
        $this->settings = $settings;
    }

    function myGenerateRandom($length)
    {
        switch (true) {
            case function_exists("random_bytes") :
                $random = random_bytes($length);
                break;
            case function_exists("openssl_random_pseudo_bytes") :
                $random = openssl_random_pseudo_bytes($length);
                break;
            case function_exists("mcrypt_create_iv") :
                $random = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
                break;
            default :
                $i = 0;
                $random = "";
                while ($i < $length):
                    $i++;
                    $random .= chr(mt_rand(0, 255));
                endwhile;
                break;
        }

        return substr(bin2hex($random), 0, $length);
    }

    function cryptMd5($value, $time = 1)
    {

        for ($i = 1; $i <= $time; $i++) {
            $value = md5($value);
        }

        return $value;

    }

    function myHash($value)
    {

        return hash_hmac('sha256', $value, SECRET_KEY);

    }

    function myControlHttps()
    {

        if (!isset($_SERVER['HTTPS'])) {

            if (strpos(strtolower($this->settings->getSettingsValue('site_url')), 'https') !== false) {
                header("Location: " . HOST);
                exit();
            }
        }
    }

    function myCmsXmlCommand($command)
    {
        switch ($command) {
            case "add_new_language":
                return true;
                break;
            case "remove_language":
                return true;
                break;
            case "add_new_style":
                return true;
                break;
            case "remove_style":
                return true;
                break;
            default:
                return false;
        }
    }

//THESE FUNCTION WORK ONLY WITH PHP 5.6

    function myCmsSecurityCreatePassword($password)
    {
        $options = [
            'cost' => $this->myCmsCalculateCost()
        ];

        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    function myCmsCalculateCost()
    {
        $target_time = 0.1;
        $cost = 5;
        do {
            $cost++;
            $timer_start = microtime(true);
            password_hash("mycmstest", PASSWORD_BCRYPT, ["cost" => $cost]);
            $timer_end = microtime(true);
        } while (($timer_end - $timer_start) < $target_time);

        return $cost;
    }

    function mySqlSecure($string)
    {
        if (get_magic_quotes_gpc()) {
            $string = stripslashes(trim($string));
        }

        $string = strip_tags(addslashes(trim($string)));
        $string = str_replace("'", "\\'", $string);
        $string = str_replace('"', '\"', $string);
        $string = str_replace(';', '\;', $string);
        $string = str_replace('--', '\--', $string);
        $string = str_replace('+', '\+', $string);
        $string = str_replace('(', '\(', $string);
        $string = str_replace(')', '\)', $string);
        $string = str_replace('=', '\=', $string);
        $string = str_replace('>', '\>', $string);
        $string = str_replace('<', '\<', $string);

        $string = str_replace('\\\\', '', $string);


        return strip_tags(trim($string));
    }

    /**
     * This function check if the file or the folder have the 0755(default) permission
     * @param $path
     * @param string $code
     * @return bool
     */
    function checkFilePermission($path, $code = "755")
    {
        clearstatcache();
        if ($code === decoct(fileperms($path) & 0777)) {
            return true;
        }

        return false;
    }

    function getFilePermission($path)
    {
        clearstatcache();

        return decoct(fileperms($path) & 0777);
    }

    /**
     * Extension types from WordPress standards
     * @return mixed
     */
    function getMimeTypes() {

        return array(
            // Image formats.
            'jpg|jpeg|jpe' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'bmp' => 'image/bmp',
            'tiff|tif' => 'image/tiff',
            'ico' => 'image/x-icon',
            // Video formats.
            'asf|asx' => 'video/x-ms-asf',
            'wmv' => 'video/x-ms-wmv',
            'wmx' => 'video/x-ms-wmx',
            'wm' => 'video/x-ms-wm',
            'avi' => 'video/avi',
            'divx' => 'video/divx',
            'flv' => 'video/x-flv',
            'mov|qt' => 'video/quicktime',
            'mpeg|mpg|mpe' => 'video/mpeg',
            'mp4|m4v' => 'video/mp4',
            'ogv' => 'video/ogg',
            'webm' => 'video/webm',
            'mkv' => 'video/x-matroska',
            '3gp|3gpp' => 'video/3gpp', // Can also be audio
            '3g2|3gp2' => 'video/3gpp2', // Can also be audio
            // Text formats.
            'txt|asc|c|cc|h|srt' => 'text/plain',
            'csv' => 'text/csv',
            'tsv' => 'text/tab-separated-values',
            'ics' => 'text/calendar',
            'rtx' => 'text/richtext',
            'css' => 'text/css',
            'htm|html' => 'text/html',
            'vtt' => 'text/vtt',
            'dfxp' => 'application/ttaf+xml',
            // Audio formats.
            'mp3|m4a|m4b' => 'audio/mpeg',
            'ra|ram' => 'audio/x-realaudio',
            'wav' => 'audio/wav',
            'ogg|oga' => 'audio/ogg',
            'flac' => 'audio/flac',
            'mid|midi' => 'audio/midi',
            'wma' => 'audio/x-ms-wma',
            'wax' => 'audio/x-ms-wax',
            'mka' => 'audio/x-matroska',
            // Misc application formats.
            'rtf' => 'application/rtf',
            'js' => 'application/javascript',
            'pdf' => 'application/pdf',
            'swf' => 'application/x-shockwave-flash',
            'class' => 'application/java',
            'tar' => 'application/x-tar',
            'zip' => 'application/zip',
            'gz|gzip' => 'application/x-gzip',
            'rar' => 'application/rar',
            '7z' => 'application/x-7z-compressed',
            'exe' => 'application/x-msdownload',
            'psd' => 'application/octet-stream',
            'xcf' => 'application/octet-stream',
            // MS Office formats.
            'doc' => 'application/msword',
            'pot|pps|ppt' => 'application/vnd.ms-powerpoint',
            'wri' => 'application/vnd.ms-write',
            'xla|xls|xlt|xlw' => 'application/vnd.ms-excel',
            'mdb' => 'application/vnd.ms-access',
            'mpp' => 'application/vnd.ms-project',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
            'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
            'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
            'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
            'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
            'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
            'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
            'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
            'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
            'sldm' => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
            'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote',
            'oxps' => 'application/oxps',
            'xps' => 'application/vnd.ms-xpsdocument',
            // OpenOffice formats.
            'odt' => 'application/vnd.oasis.opendocument.text',
            'odp' => 'application/vnd.oasis.opendocument.presentation',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'odg' => 'application/vnd.oasis.opendocument.graphics',
            'odc' => 'application/vnd.oasis.opendocument.chart',
            'odb' => 'application/vnd.oasis.opendocument.database',
            'odf' => 'application/vnd.oasis.opendocument.formula',
            // WordPerfect formats.
            'wp|wpd' => 'application/wordperfect',
            // iWork formats.
            'key' => 'application/vnd.apple.keynote',
            'numbers' => 'application/vnd.apple.numbers',
            'pages' => 'application/vnd.apple.pages',
        );
    }
}

<?php
    /**
     * User: tuttarealstep
     * Date: 10/04/16
     * Time: 0.08
     */

    namespace MyCMS\App\Utils\Security;

    if (!defined("MY_CMS_PATH")) {
        die("NO SCRIPT");
    }

    class MyCMSSecurity
    {
        private $settings;

        function __construct($settings)
        {
            $this->settings = $settings;
        }

        function my_generate_random($length)
        {
            switch (true) {
                case function_exists("mcrypt_create_iv") :
                    $random = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
                    break;
                case function_exists("openssl_random_pseudo_bytes") :
                    $random = openssl_random_pseudo_bytes($length);
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

        function crypt_md5($value, $time = 1)
        {

            for ($i = 1; $i <= $time; $i++) {
                $value = md5($value);
            }

            return $value;

        }

        function my_hash($value)
        {

            return hash_hmac('sha256', $value, SECRET_KEY);

        }

        function my_control_https()
        {

            if (!isset($_SERVER['HTTPS'])) {

                if (strpos(strtolower($this->settings->get_settings_value('site_url')), 'https') !== false) {
                    header("Location: " . HOST);
                    exit();
                }
            }
        }

        function my_cms_xml_command($command)
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
        function my_cms_calculate_cost()
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


        function my_cms_security_create_password($password)
        {
            $options = [
                'cost' => $this->my_cms_calculate_cost()
            ];

            return password_hash($password, PASSWORD_BCRYPT, $options);
        }

        function my_sql_secure($string)
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
    }

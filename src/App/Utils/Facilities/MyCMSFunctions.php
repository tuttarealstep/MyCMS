<?php
    /**
     * User: tuttarealstep
     * Date: 10/04/16
     * Time: 0.19
     */

    namespace MyCMS\App\Utils\Facilities;

    use RecursiveDirectoryIterator;
    use RecursiveIteratorIterator;

    if (!defined("MY_CMS_PATH")) {
        die("NO SCRIPT");
    }

    class MyCMSFunctions
    {
        function remove_space($string)
        {
            $space = str_replace('_', ' ', $string);

            return $space;
        }

        function add_space($string)
        {
            $space = str_replace(' ', '_', $string);

            return $space;
        }

        function time_normal_full($string)
        {
            $space = date('d/m/Y H.i.s', $string);

            return $space;
        }

        function time_normal_his($string)
        {
            $space = date('H.i.s', $string);

            return $space;
        }

        function time_normal($string)
        {
            $space = date('d/m/Y', $string);

            return $space;
        }

        function fix_text($string)
        {
            $unwanted_array = array('Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
                                    'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
                                    'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
                                    'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
                                    'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y');
            $string = strtr($string, $unwanted_array);

            return $string;
        }

        function remove_dir($dir)
        {
            $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it,
                RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($dir);
        }


        /**
         * Convert windows backslashes in forward-slashes
         * @param $path
         * @return mixed
         */
        function fixPath($path)
        {
            $path = str_replace("\\", "/", $path);

            return $path;
        }

        /**
         * Test function
         * @param $msg
         */
        function testEcho($msg)
        {
            echo $msg;
        }

        /**
         * Test function return
         * @param $msg
         * @return mixed
         */
        function testReturn($msg)
        {
            return $msg;
        }

        function stringUrlDecode($string)
        {
            return urldecode($string);
        }
    }
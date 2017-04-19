<?php
/**
 * User: tuttarealstep
 * Date: 10/04/16
 * Time: 11.46
 */

namespace MyCMS\App\Utils\Api;

class MyCMSApi
{
    //Todo add more api and support for multiple things or remove them.

    /**
     * @var
     */
    private $container;

    /**
     * MyCMSApi constructor.
     * @param $container
     */
    function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param bool $return
     * @return bool|string
     */
    function showApi($return = false)
    {

        $is_api = $this->isApi(null, true);

        if ($is_api["return"] == "true") {
            $show_api = $this->typeApi($is_api["url"]);

            if (empty($show_api)) {
                $show_api = $this->apiError();
            } elseif ($show_api == "null") {
                $show_api = $this->apiError();
            }

            header('Content-Type: application/json');
            if ($return) {
                return json_encode($show_api, JSON_PRETTY_PRINT);
            } else {
                die(json_encode($show_api, JSON_PRETTY_PRINT));
            }
        }

        return false;
    }

    /**
     * @param null $url
     * @param bool $return_array
     * @return array|bool
     */
    function isApi($url = null, $return_array = false)
    {
        if (empty($url)) {
            if (!isset($_SERVER["REQUEST_URI"])) {
                $_SERVER["REQUEST_URI"] = "/";
            }
            $url = $this->container['functions']->fixText(htmlspecialchars(substr((string)$_SERVER["REQUEST_URI"], 1)));
        } else {
            $url = $this->container['functions']->fixText(htmlspecialchars($url));
        }

        if ($return_array) {
            if (preg_match("/_api_\\//i", $url)) {
                return ["return" => "true", "url" => $url];
            }

            return ["return" => "false", "url" => $url];
        } else {
            if (preg_match("/_api_\\//i", $url)) {
                return true;
            }

            return false;
        }
    }

    /**
     * @param $url
     * @return array|bool
     */
    function typeApi($url)
    {

        $preg = "_api_/";
        $banned_settings_id = ["6", "8", "9", "10", "11", "12"];

        if (preg_match("/_api_/users//i", $url)) {


        } elseif (preg_match("/_api_/web_site//i", $url)) {

            preg_match("/_api_/web_site/(.*)/i", $url, $match);

            if (is_numeric($match["1"])) {
                $count = $this->container['database']->single("SELECT COUNT(settings_name) FROM my_cms_settings WHERE settings_id = :settings_id", ["settings_id" => $this->container['security']->mySqlSecure($match['1'])]);
            } else {
                $count = $this->container['database']->single("SELECT COUNT(settings_name) FROM my_cms_settings WHERE settings_name = :settings_name", ["settings_name" => $this->container['security']->mySqlSecure($match['1'])]);
            }

            if ($count > 0) {
                if (is_numeric($match["1"])) {
                    $settings = $this->container['database']->query("SELECT * FROM my_cms_settings WHERE settings_id = :settings_id", ["settings_id" => $this->container['security']->mySqlSecure($match['1'])]);
                } else {
                    $settings = $this->container['database']->query("SELECT * FROM my_cms_settings WHERE settings_name = :settings_name", ["settings_name" => $this->container['security']->mySqlSecure($match['1'])]);
                }
                foreach ($settings as $type) {
                    if (!in_array($type["settings_id"], $banned_settings_id)) {
                        if (is_numeric($match["1"])) {
                            if (preg_match("/" . $preg . "web_site/" . $type["settings_id"] . "/i", $url)) {
                                $info = ["settings_id" => $type["settings_id"], "settings_name" => $type["settings_name"], "settings_value" => $type["settings_value"]];
                            }
                        } else {
                            if (preg_match("/" . $preg . "web_site/" . $type["settings_name"] . "/i", $url)) {
                                $info = ["settings_id" => $type["settings_id"], "settings_name" => $type["settings_name"], "settings_value" => $type["settings_value"]];
                            }
                        }
                    } else {
                        return $this->apiError("Private setting. This setting is private for the security of the website or is a useless setting for you.", "PrivateWebSiteSetting", "00111");
                    }
                }
                if (empty($info)) {
                    return $this->apiError();
                } elseif ($info == "null") {
                    return $this->apiError();
                }

                return $info;
            } else {
                return $this->apiError("Requested setting not found. ", "NoWebSiteSettingFound", "00110");
            }
        } elseif (preg_match("/_api_\/blog\//i", $url)) {

        } else {
            return $this->apiError();
        }

        return false;

    }

    /**
     * @param string $message
     * @param string $type
     * @param string $code
     * @return array
     */
    function apiError($message = "Request type not found.", $type = "NoApiTypeFound", $code = "0010")
    {
        return ["error" => ["message" => $message, "type" => $type, "code" => $code]];
    }
}
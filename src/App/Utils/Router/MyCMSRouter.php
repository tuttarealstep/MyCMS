<?php
/**
 * User: tuttarealstep
 * Date: 10/03/17
 * Time: 17.38
 */

namespace MyCMS\App\Utils\Router;

use MyCMS\App\Utils\Management\MyCMSContainer;

class MyCMSRouter extends MyCMSContainer
{
    function __construct($container)
    {
        $this->setContainer($container);
    }

    function getUrl($return = true)
    {

        $url_complete = explode('/', $_SERVER['REQUEST_URI']);
        $script_complete = explode('/', $_SERVER['SCRIPT_NAME']);

        for ($i = 0; $i < count($script_complete);) {
            if (@$url_complete[ $i ] == @$script_complete[ $i ]) {
                unset($url_complete[ $i ]);
            }
            $i++;
        }

        @$url_value = array_values($url_complete);
        switch ($url_value[0]):
            default:
                $url_return = ((isset($url_value[0])) && ($url_value[0] != '')) ? $this->container['security']->mySqlSecure($url_value[0]) : 'index';
                break;
        endswitch;

        //print_r($url_return);

        if (strpos($url_value[ count($url_value) - 1 ], "?") !== false) {
            //Found ( "?" )
            @$url_get_value = explode("?", $url_value[ count($url_value) - 1 ]);
            $url_value[ count($url_value) - 1 ] = $url_get_value[0];
            unset($url_get_value[0]);
            @$url_value[] = implode("&", $url_get_value);
        }

        unset($url_value[0]); //Remove page from url_value

        //die(print_r($url_value));
        foreach ($url_value as $get_key => $value) {
            if (is_numeric($get_key)) {
                @$get_value = $url_value[ $get_key - 1 ];
                if (isset($get_value) && !empty($get_value)) {
                    @$_GET[ $get_value ] = urldecode($value);
                    $get_value = '';
                }
            }
        }

        if ($return == true) {
            return $url_return;
        }

        return null;
    }
}
<?php
    /**
     * User: tuttarealstep
     * Date: 09/04/16
     * Time: 15.38
     */

    namespace MyCMS\App\Utils\Exceptions;

    class MyCMSException extends \Exception
    {
        public static $error_page = <<<ERROR
<html>
    <head>
        <title>MyCMS Error</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <style type="text/css">
            body {
            background-color: #fff;
                color: #000;
                font-size: 0.9em;
                font-family: sans-serif,helvetica;
                margin: 0;
                padding: 0;
            }
            h1 {
            text-align: center;
                margin: 0;
                padding: 0.6em 2em 0.4em;
                background-color: #294172;
                color: #fff;
                font-weight: normal;
                font-size: 1.75em;
                border-bottom: 2px solid #000;
            }
            h1 strong {
            font-weight: bold;
                font-size: 1.5em;
            }
            h3 {
            text-align: center;
                background-color: #ff0000;
                padding: 0.5em;
                color: #fff;
            }
            .content {
            padding: 1em 5em;
            }
        </style>
    </head>

    <body>
        <h1><strong>MyCMS - Error!</strong></h1>

        <div class="content">
            <h3>{@error_title@}</h3>
            <p><strong>{@error_message@}</strong></p>
        </div>
    </body>
</html>
ERROR;

        public function __construct($message, $title = null, $code = null, $previous = null)
        {

            $error_page = self::$error_page;
            $message = nl2br($message);

            if ($title == null) {
                $page = str_replace('{@error_title@}', 'Fatal Error', $error_page);
            } else {
                $page = str_replace('{@error_title@}', $title, $error_page);
            }

            $page = str_replace('{@error_message@}', $message, $page);

            echo $page;
        }

        public static function nullHandler(\Exception $e)
        {
        }
    }

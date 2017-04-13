<?php
    /*                     *\
    |	MYCMS - TProgram    |
    \*                     */

    //HOSTNAME
    define("C_HOST", "localhost");

    //DATABASE USER
    define("C_USER", "root");

    //DATABASE PASSWORD
    define("C_PASSWORD", "");

    //DATABASE NAME
    define("C_DATABASE", "");


//OTHERS
    define("MY_M_DEBUG", false);  //If true show all errors

//KEY

    define('SESSION_KEY_GENERATE', true);
    define('SESSION_KEY', 'MYCMS_00000'); //EDIT THIS

    define('SECRET_KEY', '00000000000000000000000000000000000000000000000000'); //EDIT THIS

    define('CRYPT_KEY', '00000000000000000000000000000000000000000000000000'); //EDIT THIS

//THEME
    define('ENABLE_TWIG_TEMPLATE_ENGINE', true);
    define('ENABLE_TWIG_TEMPLATE_DEBUG', false);

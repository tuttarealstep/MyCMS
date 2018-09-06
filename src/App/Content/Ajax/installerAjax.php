<?php
define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);

ini_set("display_errors", 1);
error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING);

require_once dirname(__FILE__) . "/../../Utils/Helper/MyCMSSchema.php";

if (!file_exists(dirname(__FILE__) . "/../../Configuration/my_config.php"))
{
    session_start();

    $configHost = $_POST['configHost'];
    $configUser = $_POST['configUser'];
    $configPassword = $_POST['configPassword'];
    $configDatabase = $_POST['configDatabase'];
    $configSiteUrl = $_POST['configSiteUrl'];

    $_SESSION['installerSiteUrl'] = $configSiteUrl;
    if(
        !isset($_POST['configHost']) ||
        !isset($_POST['configUser']) ||
        !isset($_POST['configPassword']) ||
        !isset($_POST['configDatabase']) ||
        !isset($_POST['configSiteUrl'])
    )
    {
        echo "false";
    } else {
        $schema = new MyCMSSchema(null);
        try
        {
            $connection = new PDO("mysql:host=$configHost;dbname=$configDatabase", $configUser, $configPassword, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
            if(!$connection)
            {
                echo "false";
                return;
            } else {
                $connection->exec($schema->dropDatabaseTables());
                $connection->exec($schema->databaseSchema());
            }
        } catch (Exception $e)
        {
            echo $e;
            echo "false";
            return;
        }

        $check = $schema->writeConfig($configHost, $configUser, $configPassword, $configDatabase);

        if($check === true)
        {
            echo "true";
        } else {
            echo $check;
        }
    }
} else {
    include '../../../../src/Bootstrap.php';

    if(!isset($_SESSION['MY_CMS_INSTALLER']))
    {
        die();
    }

    if(
        !isset($_POST['user_name']) ||
        !isset($_POST['user_surname']) ||
        !isset($_POST['user_password']) ||
        !isset($_POST['user_mail'])
    ) {
        echo "false";
    } else {

        $schema = new MyCMSSchema($app->container);
        $schema->setDatabaseValues();
        $schema->setSettings("MyCMS 6", $_SESSION['installerSiteUrl']);
        $schema->setRoles();

        $user_name = $_POST['user_name'];
        $user_surname = $_POST['user_surname'];
        $options = [
            'cost' => 8
        ];
        $user_password = password_hash($_POST['user_password'], PASSWORD_BCRYPT, $options);
        $user_mail = $_POST['user_mail'];
        $ip = $_SERVER['REMOTE_ADDR'];

        $app->container['database']->query("INSERT INTO my_users (name, surname, password, mail, ip, rank) VALUES ('" . $user_name . "', '" . $user_surname . "', '" . $user_password . "', '" . $user_mail . "', '" . $ip . "', 'administrator')");

        echo "true";
    }
}
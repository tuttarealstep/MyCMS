<?php
/*                     *\
|	MyCMS    |
\*                     */

if (!defined('PAGE_NAME')):
    $page_name = '';
else:
    $page_name = ': ' . PAGE_NAME;
endif;

?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php
    if (!defined("NO_VIEWPORT")) {
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
    }
    ?>
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{@siteURL@}/src/App/Utils/MyCMS.ico">
    <title>{@siteNAME@}<?php echo $page_name ?></title>
    <link href="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/css/lato.css" rel="stylesheet">
    <?php $this->getStyleScriptAdmin('css'); ?>
    <?php $this->container['plugins']->applyEvent('adminHead'); ?>
    <?php $this->noRobots(); ?>

    <!--[if lt IE 9]>
    <script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/html5shiv.js"></script>
    <script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/respond.min.js"></script>
    <![endif]-->

    <?php
    if (isset($_SESSION['user']['id'])) {


        switch ($this->container['users']->getInfo($_SESSION['user']['id'], 'adminColor')) {
            default:
            case 'default':

                ?>
                <link id="adminStyle" href="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/css/MyAdmin.css" rel="stylesheet">
                <?php
                break;
            case 'Light':
                ?>
                <link id="adminStyle" href="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/css/MyAdminLight.css" rel="stylesheet">
                <?php
                break;
                ?>
                <?php
        }
    } else {
        ?>
        <link id="adminStyle" href="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/css/MyAdmin.css" rel="stylesheet">
        <?php
    }
    ?>
    <script>
        var myBasePath = "<?php echo MY_BASE_PATH; ?>";
    </script>

</head>

<?php

$this->noRobots();
//todo update this page with new method
$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("update_cms"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

if (!$this->container['theme']->thereIsNewUpdate()) {
    header("location: " . HOST . "/my-admin/home");
}


define('PAGE_ID', 'admin_update_page');
define('PAGE_NAME', $this->container['languages']->ea('page_update_page_name', '1'));

$this->getFileAdmin('header');

$this->getStyleScriptAdmin('script');

//PHP SETTINGS
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 3000);
set_time_limit(0);
ignore_user_abort(true);


$progress = 0;
$download_mode = false;


function folderCopy($src, $dst)
{
    if (is_dir($src)) {
        @mkdir($dst, 0777, true);
        $files = scandir($src);
        foreach ($files as $file)
            if ($file != "." && $file != ".." && $file != "tmp" && $file != ".idea")
                folderCopy("$src/$file", "$dst/$file");
    } else {
        copy($src, $dst);
    }
}

function delete_dir($path)
{
    return is_file($path) ? unlink($path) : array_map(__FUNCTION__, glob($path . '/*')) == @rmdir($path);
}

function zip_folder($source, $destination)
{
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file) {
            $file = str_replace('\\', '/', $file);

            // Ignore "." and ".." folders
            if (in_array(substr($file, strrpos($file, '/') + 1), ['.', '..', '.idea', 'tmp', '.idea/']))
                continue;

            $file = realpath($file);


            if (is_dir($file) === true) {

                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            } else if (is_file($file) === true) {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    } else if (is_file($source) === true) {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}

function back_up_cms_folder()
{
    @mkdir(B_PATH_S . '/tmp/', 0755, true);
    zip_folder(B_PATH_S . '/', B_PATH_S . '/tmp/my_cms_backup_' . date('Y-m-d__H-i-s', time()) . '_' . md5(SECRET_KEY) . '.zip');
}

function r_removeDir_for_update($dir)
{
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file)
            if ($file != "." && $file != "..") {
                if (is_dir($dir . "/" . $file)) {
                    r_removeDir_for_update($dir . "/" . $file);
                } else {
                    unlink($dir . "/" . $file);
                }
            }
        rmdir($dir);
    }
}

function removeDir_for_update($dir)
{
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file)
            if ($file != "." && $file != "..") $this->container['functions']->removeDir("$dir/$file");
        rmdir($dir);
    }
}

function remove_for_update($src = B_PATH_S)
{
    if ($src != B_PATH_S . "/src/App/Configuration" || $src != B_PATH_S . "/src/App/Content/Theme" || $src != B_PATH_S . "/src/App/Content/Plugins") {
        if ($src != B_PATH_S . "/test") { //TMP
            if (is_dir($src)) {
                $files = scandir($src);
                foreach ($files as $file)
                    if ($file != "." && $file != ".." && $file != "tmp" && $file != ".idea")
                        remove_for_update("$src/$file");
            } else if (file_exists($src))
                unlink($src);
        }
    }
}

function download_update()
{
    $filename_cms = '';
    $filename_db = '';

    $info = $this->container['theme']->thereIsNewUpdate(false);
    if ($info[2] != '' && $info[3] != '') {
        if ($info[1] == 'all_update') {
            $filename_cms = "my_cms_" . $info[2];
            $filename_db = "my_cms_database_" . $info[3];
        }
    }
    if ($info[2] != '' && $info[3] == '') {
        if ($info[1] == 'core_update') {
            $filename_cms = "my_cms_" . $info[2];
            $filename_db = '';
        }
    }
    if ($info[2] == '' && $info[3] != '') {
        if ($info[1] == 'db_update') {
            $filename_cms = '';
            $filename_db = "my_cms_database_" . $info[3];
        }
    }

    $tmp_path = B_PATH_S . "/tmp";
    $download_url = MY_CMS_WEBSITE . "/update/";
    $remove_folder = false;

    if ($filename_cms != '' && $filename_db != '') {
        $info_cms = file_get_contents($download_url . $filename_cms . '.zip');
        $info_db = file_get_contents($download_url . $filename_db . '.zip');
        file_put_contents($tmp_path . '/' . $filename_cms . '.zip', $info_cms);
        file_put_contents($tmp_path . '/' . $filename_db . '.zip', $info_db);

        $zip_extract = new ZipArchive;
        if ($zip_extract->open($tmp_path . '/' . $filename_cms . '.zip') === true) {
            $zip_extract->extractTo($tmp_path . '/' . $filename_cms);
            $zip_extract->close();
            $remove_folder_1 = true;
        } else {
            $remove_folder_1 = false;
            //Error
        }
        if ($zip_extract->open($tmp_path . '/' . $filename_db . '.zip') === true) {
            $zip_extract->extractTo($tmp_path . '/' . $filename_db);
            $zip_extract->close();
            $remove_folder_2 = true;
        } else {
            $remove_folder_2 = false;
            //Error
        }
        if ($remove_folder_1 == true && $remove_folder_2 == true) {
            unlink($tmp_path . '/' . $filename_db . '.zip');
            unlink($tmp_path . '/' . $filename_cms . '.zip');
            $remove_folder = true;
        }

    }
    if ($filename_cms != '' && $filename_db == '') {
        $info_cms = file_get_contents($download_url . $filename_cms . '.zip');
        file_put_contents($tmp_path . '/' . $filename_cms . '.zip', $info_cms);
        $zip_extract = new ZipArchive;
        if ($zip_extract->open($tmp_path . '/' . $filename_cms . '.zip') === true) {
            $zip_extract->extractTo($tmp_path . '/' . $filename_cms);
            $zip_extract->close();
            $remove_folder = true;
            unlink($tmp_path . '/' . $filename_cms . '.zip');
        } else {
            //Error
        }

    }
    if ($filename_cms == '' && $filename_db != '') {
        $info_db = file_get_contents($download_url . $filename_db . '.zip');
        file_put_contents($tmp_path . '/' . $filename_db . '.zip', $info_db);
        $zip_extract = new ZipArchive;
        if ($zip_extract->open($tmp_path . '/' . $filename_db . '.zip') === true) {
            $zip_extract->extractTo($tmp_path . '/' . $filename_db);
            $zip_extract->close();
            $remove_folder = true;
            unlink($tmp_path . '/' . $filename_db . '.zip');
        } else {
            //Error
        }

    }

    if ($remove_folder) {
        remove_for_update(P_PATH_S . "/App/MyAdmin");
        remove_for_update(P_PATH_S . "/App/Utils");
        remove_for_update(P_PATH_S . "/App/Content/Ajax");
        remove_for_update(P_PATH_S . "/App/Content/Storage/Logs");

        removeDir_for_update(P_PATH_S . "/App/MyAdmin");
        removeDir_for_update(P_PATH_S . "/App/Utils");
        removeDir_for_update(P_PATH_S . "/App/Content/Ajax");
        removeDir_for_update(P_PATH_S . "/App/Content/Storage/Logs");

        r_removeDir_for_update(B_PATH_S . "/vendor");

        folderCopy($tmp_path . '/' . $filename_cms, B_PATH_S);

        remove_for_update($tmp_path . '/' . $filename_cms);
        removeDir_for_update($tmp_path . '/' . $filename_cms);

    }


}

if (isset($_POST['update_button'])) {
    /* <script>alert("<?php $this->container['languages']->ea('page_update_alert') ?>");</script> */
    $download_mode = true;
    back_up_cms_folder(); //Make backup
    download_update(); //download update
    sleep(2);
    echo '<meta http-equiv="refresh" content="5;url=' . HOST . '/my-admin/home"> ';
}
?>
<body>
<?php $this->container['plugins']->applyEvent('updateBody'); ?>
<div id="wrapper">
    <div class="row" style="margin-top: 50px;">
        <div class="col-lg-6" style="float: none; margin: auto;">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form action="" method="post">
                        <div class="row" style="margin-left: 10px; margin-right: 10px;">
                            <img src="{@siteURL@}/src/App/Utils/MyCMS_logo.png" style="height: 60px; width: 150px;"/>
                            <?php if (!$download_mode) { ?>
                                <a href="{@siteURL@}/my-admin/home"
                                   style="float: right; margin-top: 26px;"><?php $this->container['languages']->ea('page_update_return_back') ?></a>
                                <input name="update_button" type="submit"
                                       style="float: right; margin-top: 20px; margin-right: 20px;" class="btn btn-info"
                                       value="<?php $this->container['languages']->ea('page_update_update_button') ?>"/>
                            <?php } ?>
                        </div>
                    </form>
                    <?php if ($download_mode){ ?>
                        <div class="row" style="margin-left: 10px; margin-right: 10px; margin-top: 10px;">
                            <div class="col-lg-12">
                                <?php $this->container['languages']->ea('page_update_info_process') ?>
                            </div>
                        </div>
                    <?php } else { ?>
                    <div class="row" style="margin-left: 10px; margin-right: 10px; margin-top: 10px;">
                        <div class="alert alert-danger"><span class="badge" style="background-color: red">!</span> <b>MyCMS
                                Update System is in development! (This file need 777 permission)</b></div>
                        <div class="col-lg-6">
                            <?php
                            $info = $this->container['theme']->thereIsNewUpdate(false);
                            if ($info[2] != '' && $info[3] != '') {
                                if ($info[1] == 'all_update') {
                                    echo "<h2>MyCMS <b>$info[2]</b> Database <b>$info[3]</b></h2>(Database - Core)";
                                }
                            }
                            if ($info[2] != '' && $info[3] == '') {
                                if ($info[1] == 'core_update') {
                                    echo "<h2>MyCMS <b>$info[2]</b> </h2>(Core)";
                                }
                            }
                            if ($info[2] == '' && $info[3] != '') {
                                if ($info[1] == 'db_update') {
                                    echo "<h2>Database <b>$info[3]</b> </h2>(Database)";
                                }
                            }
                            ?>
                        </div>
                        <div class="col-lg-6">
                            <h2><b><?php $this->container['languages']->ea('page_update_changelog') ?></b></h2>
                            <ul>
                                <?php
                                foreach ($info[4] as $info_row) { ?>
                                    <?php if (!empty($info_row[ $info[3] ])) { ?>
                                        <li>
                                            <span class="badge">DB <?php echo $info[3]; ?></span> <?php echo $info_row[ $info[3] ]; ?>
                                        </li> <?php } ?>
                                    <?php if (!empty($info_row[ $info[2] ])) { ?>
                                        <li>
                                            <span class="badge">CMS <?php echo $info[2]; ?></span> <?php echo $info_row[ $info[2] ]; ?>
                                        </li> <?php } ?>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $this->getFileAdmin('footer'); ?>
</body>
</html>
<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */

$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("manage_links"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

define('PAGE_ID', 'admin_menu');
define('PAGE_NAME', $this->container['languages']->ea('page_menu_page_name', '1'));

$this->container['theme']->addStyleScriptAdmin('css', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/css/icon-picker.min.css');
$this->container['theme']->addStyleScriptAdmin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/iconPicker.min.js');
$this->container['theme']->addStyleScriptAdmin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/jquery-ui.min.js');

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');

$info = "";

$this->getStyleScriptAdmin('script');

if (isset($_POST['newmenu'])) {


    if (!empty($_POST['name'])) {

        $name = addslashes($_POST['name']);
        $pageNAMEURL = addslashes($_POST['url']);
        $personal_url = addslashes($_POST['personal_url']);
        $selected_icon = addslashes($_POST['selected_icon']);

        if ($pageNAMEURL == "empty") {

            if (!empty($personal_url)) {

                $idpagina = $this->container['database']->single("SELECT pageID_MENU FROM my_page WHERE pageTITLE = '" . $pageNAMEURL . "' LIMIT 1");

                if (!empty($selected_icon)) {
                    $this->container['database']->query("INSERT INTO my_menu (menu_name, menu_page_id, menu_link, menu_sort, menu_icon, menu_icon_image) VALUES ('$name', '$idpagina', '$personal_url', '0', 'glyphicon','$selected_icon')");
                } else {
                    $this->container['database']->query("INSERT INTO my_menu (menu_name, menu_page_id, menu_link, menu_sort,menu_icon_image) VALUES ('$name', '$idpagina', '$personal_url', '0', '')");
                }
                $info = '<div class="alert alert-success">' . $this->container['languages']->ea('page_menu_add_success', '1') . '</div>';
                $name = '';
                $personal_url = '';

            } else {
                $info = '<div class="alert alert-danger">' . $this->container['languages']->ea('page_menu_error_empty_personal_url', '1') . '</div>';
                $name = addslashes($_POST['name']);
                $personal_url = addslashes($_POST['personal_url']);
            }

        } else {


            $idpagina = $this->container['database']->single("SELECT pageID_MENU FROM my_page WHERE pageTITLE = '" . $pageNAMEURL . "' LIMIT 1");
            $page_url = $this->container['database']->single("SELECT pageURL FROM my_page WHERE pageTITLE = '" . $pageNAMEURL . "' LIMIT 1");
            if (!empty($selected_icon)) {
                $this->container['database']->query("INSERT INTO my_menu (menu_name, menu_page_id, menu_link, menu_sort, menu_icon,menu_icon_image) VALUES ('$name', '$idpagina', '$page_url', '0', 'glyphicon','$selected_icon')");
            } else {
                $this->container['database']->query("INSERT INTO my_menu (menu_name, menu_page_id, menu_link, menu_sort,menu_icon_image) VALUES ('$name', '$idpagina', '$page_url', '0', '')");
            }
            $info = '<div class="alert alert-success">' . $this->container['languages']->ea('page_menu_add_success', '1') . '</div>';
            $name = '';
            $personal_url = '';
        }

    } else {
        $info = '<div class="alert alert-danger">' . $this->container['languages']->ea('page_menu_error_add_name', '1') . '</div>';
        $name = addslashes($_POST['name']);
        $personal_url = addslashes($_POST['personal_url']);
    }
}
?>
<script>
    function updateSort() {

        menulist = $('#menuedit').sortable('serialize');
        $.ajax({
            url: "{@siteURL@}/src/App/Content/Ajax/menu_update.php",
            type: "post",
            data: menulist,
            error: function () {
                alert("AJAX ERROR");
            }
        });
    }

    $(document).ready(
        function () {
            $("#menuedit").sortable({
                update: function () {
                    menulist = $('#menuedit').sortable('serialize');
                    $.ajax({
                        url: "{@siteURL@}/src/App/Content/Ajax/menu_update.php",
                        type: "post",
                        data: menulist,
                        error: function () {
                            alert("AJAX ERROR");
                        }
                    });
                }
            });

            $('.menuButtonUp').click(function () {
                var current = $(this).closest('li');
                current.prev().before(current);
                updateSort();
            });
            $('.menuButtonDown').click(function () {
                var current = $(this).closest('li');
                current.next().after(current);
                updateSort();
            });
        }
    );
    $(function () {
        $(".icon-picker").iconPicker();
    });
</script>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <?php if (!empty($info)) {
                echo '<br>' . $info . '<br>';
            } ?>
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ta('page_menu_header'); ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <ol id="menuedit">
                <?php
                $menu = $this->container['database']->query("SELECT * FROM `my_menu` WHERE `menu_enabled` = '1' ORDER BY `menu_sort` ASC");
                $i = 0;
                foreach ($menu as $row) {
                    $i++;
                    $candelete = $row['menu_can_delete'];
                    ?>
                    <li id="menu_<?php echo $row['menu_id']; ?>">
                        <div class="alert alert-info">
                            <b><?php echo $row['menu_name']; ?></b><?php if ($candelete == '1'): ?> - <a
                                style="color:#F00"
                                href="{@siteURL@}/my-admin/delete-menu/<?php echo $row['menu_id']; ?>"><?php $this->container['languages']->ea('page_menu_delete'); ?></a><?php endif; ?>
                            <button class="menuButtonDown btn pull-right btn-xs"><?php $this->container['languages']->ea('page_menu_down'); ?></button>
                            <button class="menuButtonUp btn pull-right btn-xs"
                                    style="margin-right: 10px"><?php $this->container['languages']->ea('page_menu_up'); ?></button>
                        </div>
                    </li>
                    <?php
                }
                ?>
            </ol>
        </div>
        <!-- /.col-lg-6 -->

        <div class="col-lg-4 col-lg-offset-2 col-md-4 col-md-offset-2">
            <div class="panel b_panel">
                <div class="panel-heading">
                    <h1 class="panel-title text-center"><?php $this->container['languages']->ea('page_menu_add_new_menu'); ?></h1>
                </div>
                <form action="" method="post">
                    <div class="panel-body b_panel-body">
                        <div class="panel-body-padding">

                            <input type="text" name="name" class="form-control b_form-control" maxlength="100"
                                   placeholder="<?php $this->container['languages']->ea('page_menu_add_new_menu_name'); ?>"
                                   value="<?php echo isset($name) ? $name : ""; ?>">
                            <br/>
                            <span class="label label-success"><?php $this->container['languages']->ea('page_menu_add_new_menu_selectpage'); ?></span>
                            <br/>
                            <br/>
                            <select name="url" class="form-control b_form-control">
                                <option value="empty"><?php $this->container['languages']->ea('page_menu_empty_page'); ?></option>
                                <?php
                                $page = $this->container['database']->query("SELECT * FROM my_page WHERE pagePUBLIC = '1'");
                                $i = 0;
                                foreach ($page as $pagerow) {
                                    $i++;
                                    ?>
                                    <option value="<?php echo $pagerow['pageTITLE']; ?>"><?php echo $this->container['functions']->removeSpace($pagerow['pageTITLE']); ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <br/>
                            <input type="text" name="personal_url" placeholder="<?php $this->container['languages']->ea('page_menu_personal_url'); ?>"
                                   class="b_form-control form-control" maxlength="250"
                                   value="<?php echo isset($personal_url) ? $personal_url : "" ?>">
                            <br/>
                            <span class="label label-success"><?php $this->container['languages']->ea('page_menu_icon'); ?></span>
                            <br/>
                            <br/>
                            <input type="text" name="selected_icon" class="b_form-control icon-picker"/>

                        </div>
                    </div>
                    <button type="submit" name="newmenu"
                            class="btn btn-primary btn-block b_btn b_btn_radius"><?php $this->container['languages']->ea('page_menu_add_button'); ?></button>
                </form>
            </div>
        </div>


    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

</body>

</html>


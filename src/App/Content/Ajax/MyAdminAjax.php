<?php
    define('MY_CMS_PATH', true);
    define("LOADER_LOAD_PAGE", false);
    include '../../../../src/Bootstrap.php';

    if (staff_logged_in()) {
        function newCategory($category, $App)
        {
            $finder = $App->container['blog']->categoryfinder($category);
            if ($finder == true) {
                echo "duplicate";
            } else {
                $App->container['database']->query("INSERT INTO my_blog_category (catNAME,catDESCRIPTION) VALUES (:name, :description)", array('name' => $category, 'description' => ""));

                $cat = $App->container['database']->query("SELECT * FROM my_blog_category");
                foreach ($cat as $category) {
                    ?>
                    <option value="<?php echo $category['catNAME']; ?>"><?php echo $category['catNAME']; ?></option>
                    <?php
                }
            }
        }

        if (isset($_POST['m'])) {
            switch ($_POST['m']) {
                case 'newCategory':
                    if (isset($_POST['category']) && !empty($_POST['category'])) {
                        newCategory($App->container['security']->my_sql_secure($_POST['category']), $App);
                    }
                    break;
                case 'customizerKeepSession':
                    $_SESSION['customizerLastAction'] = time();
                    break;
                case 'checkMyPageExist':
                    if (isset($_POST['pageId']) && is_numeric($_POST['pageId'])) {
                        if ($App->container['page_loader']->checkIfPageExist($_POST['pageId'])) {
                            echo "true";
                        } else {
                            echo "false";
                        }
                    }
                    break;
                case 'customizerThemeSessionSet':
                    $_SESSION['customizerThemeSession']['theme'] = $App->container['security']->my_sql_secure($_POST['theme']);
                    break;
                case 'customizerThemeSessionUnset':
                    unset($_SESSION['customizerThemeSession']);
                    break;
                case 'changeAdminColor':
                    if (isset($_POST['color']) && !empty($_POST['color'])) {
                       $color = $App->container['security']->my_sql_secure($_POST['color']);
                        $App->container['users']->setInfo($_SESSION['staff']['id'], "adminColor", $color);
                    }
                    break;
            }
        }
    }
?>
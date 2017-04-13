<?php
    /*                     *\
    |	MYCMS - TProgram    |
    \*                     */
    define('MY_CMS_PATH', true);
    define("LOADER_LOAD_PAGE", false);
    include '../../../../src/Bootstrap.php';

    global $my_users, $my_db;
    hide_if_staff_not_logged();
    if (staff_logged_in()) {
        $user_rank = $my_users->getInfo($_SESSION['staff']['id'], 'rank');
        if ($user_rank < 3) {
            return;
        }

        $request_folder = my_sql_secure(s_decrypt($_GET['folder']));
        $request_theme_folder = my_sql_secure(s_decrypt($_GET['theme']));
        $request_file = my_sql_secure(s_decrypt($_GET['file']));

        if ($request_folder == "" && $request_theme_folder == "")
            return;

        if ($request_folder == "home_folder--theme") {
            $file_dir = '../Theme/' . $request_theme_folder;
        } else {
            $file_dir = '../Theme/' . $request_theme_folder . '/' . $request_folder;
        }

        $file_mode = false;
        if (!empty($request_file)) {
            $file_mode = true;
        }


        if ($file_mode == false) {
            $file_and_dir_array = array_diff(scandir($file_dir, 0), array('..', '.'));
            $sub_sub_folder = false;
            $info_explode = explode('/', $request_folder);
            //print_r($request_folder);
            echo '<br>';

            if ($info_explode[0] == "") {
                unset($info_explode[0]);
            }
            //print_r($info_explode);
            $array_info_sub = count($info_explode);
            if ($array_info_sub >= 2) {
                $sub_sub_folder = true;
            }

            $i = 1;
            $path_back = '';
            foreach ($info_explode as $path_info) {
                if ($i == (count($info_explode))) {
                    break;
                } else {
                    if ($path_info != "") {
                        $path_back = $path_back . '/' . $path_info;
                    }
                    $i++;
                }
            }
            ?>
            <h3 id="path_name" style="margin-top: -20px"><?php echo str_replace('//', '/', $request_folder); ?></h3>
            <?php if ($request_folder != "") { ?>
                <li class="list-group-item"><i class="fa fa-folder"></i> <a href="#"
                                                                            onclick="go_to_folder_home();">.</a>
                </li><?php } ?>
            <?php if ($sub_sub_folder == true) { ?>
                <li class="list-group-item"><i class="fa fa-folder"></i> <a href="#"
                                                                            onclick="show_folder('<?php echo s_crypt($path_back); ?>');">..</a>
                </li><?php } ?>
            <?php
            $dir_array = [];
            $file_array = [];
            foreach ($file_and_dir_array as $all_row) {
                if (is_dir($file_dir . '/' . $all_row)) {
                    $dir_array[] = $all_row;
                } else {
                    $file_array[] = $all_row;
                }
            }
            foreach ($dir_array as $dir_row) {
                ?>
                <li class="list-group-item"><i class="fa fa-folder"></i> <a href="#<?php if ($request_folder == "") {
                        echo $dir_row;
                    } else {
                        echo '/' . $request_folder . '/' . $dir_row;
                    } ?>" onclick="show_folder('<?php if ($request_folder == "") {
                        echo s_crypt($dir_row);
                    } else {
                        echo s_crypt('/' . $request_folder . '/' . $dir_row);
                    } ?>')" style="color: #563d7c;"><b><?php echo $dir_row; ?></b></a></li>
            <?php }
            foreach ($file_array as $file_row) { ?>
                <li class="list-group-item"><i class="fa fa-file"></i> <a href="#<?php echo $file_row; ?>"
                                                                          onclick="show_file('<?php echo s_crypt($file_row); ?>', '<?php echo s_crypt(str_replace('//', '/', $request_folder)); ?>')"><?php echo $file_row; ?></a>
                </li>
            <?php }
            ?>
            <script>
                var view = document.getElementById("#file_manager");
                view.scrollIntoView(false);
            </script>
        <?php } else { ?>
            <?php

            $file = file_get_contents($file_dir . '/' . $request_file);
            ?>
            <i class="fa fa-folder"></i> <b><a href="#" onclick="go_to_folder_home();">.</a></b><br><br>
            <textarea id="div_code_mirror" style="display: none;"><?php echo htmlspecialchars($file); ?></textarea>
            <script id="js_to_ex">
                var div_code_mirror = CodeMirror.fromTextArea(document.getElementById("div_code_mirror"), {
                    lineNumbers: true,
                    matchBrackets: true,
                    mode: "application/x-httpd-php",
                    theme: "dracula",
                    indentUnit: 4,
                    indentWithTabs: true
                });
            </script>

        <?php } ?>
    <?php } ?>

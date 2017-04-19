<?php
if (isset($_POST['admin-login'])) {
    $mail = htmlentities($App->container['security']->mySqlSecure($_POST['email']));
    $password = htmlentities($App->container['security']->mySqlSecure($_POST['password']));

    if (isset($_POST['remember'])) {
        $remember = htmlentities($App->container['security']->mySqlSecure($_POST['remember']));
    } else {
        $remember = null;
    }

    if ($remember == "remember_t") {
        $login = $App->container['users']->loginAdmin($mail, $password, true);
        if ($login["login"] == 1) {
            header("location: " . HOST . "/my-admin/index");
            exit;
        } else {
            define("INDEX_ERROR", $App->container['languages']->ea($login["error"], '1'));
        }
    } else {
        $login = $App->container['users']->loginAdmin($mail, $password, false);
        if ($login["login"] == 1) {
            header("location: " . HOST . "/my-admin/index");
            exit;
        } else {
            define("INDEX_ERROR", $App->container['languages']->ea($login["error"], '1'));
        }
    }
}

if (isset($_POST['login'])) {
    $mail = htmlentities($App->container['security']->mySqlSecure($_POST['mail']));
    $password = htmlentities($App->container['security']->mySqlSecure($_POST['password']));
    if (isset($_POST['remember'])) {
        $remember = htmlentities($App->container['security']->mySqlSecure($_POST['remember']));
    } else {
        $remember = null;
    }

    if ($remember == "remember_t") {

        $login = $App->container['users']->login($mail, $password, true);

        if ($login["login"] == 1) {
            header("location: " . HOST . "/index");
            exit;
        } else {
            define("INDEX_ERROR", $App->container['languages']->e($login["error"], true));
        }

    } else {
        $login = $App->container['users']->login($mail, $password, false);
        if ($login["login"] == 1) {
            header("location: " . HOST . "/index");
            exit;
        } else {
            define("INDEX_ERROR", $App->container['languages']->e($login["error"], true));
        }
    }
}

if (isset($_POST['register'])) {
    // Dati Inviati dal modulo
    $return_url = (isset($_POST['return_url'])) ? trim($_POST['return_url']) : '';
    $name = (isset($_POST['name'])) ? trim($_POST['name']) : '';
    $surname = (isset($_POST['surname'])) ? trim($_POST['surname']) : '';
    $password = (isset($_POST['password'])) ? trim($_POST['password']) : '';
    $email = htmlentities($_POST['mail']);

    // Filtro i dati inviati se i magic_quotes del server sono disabilitati per motivi di sicurezza
    if (!get_magic_quotes_gpc()) {
        $name = addslashes($name);
        $surname = addslashes($surname);
        $password = addslashes($password);
        $email = addslashes($email);
    }
    $register = $App->container['users']->register($App->container['security']->mySqlSecure($email), $App->container['security']->mySqlSecure($password), $App->container['security']->mySqlSecure($name), $App->container['security']->mySqlSecure($surname));
    if ($register["register"] == 1) {
        if (empty($return_url)) {
            header("location: " . HOST . "/index");
            exit;
        } else {
            header("location: " . $return_url);
            exit;
        }
    } else {
        define("INDEX_ERROR", $App->container['languages']->e($register["error"], true));
    }
}
if (isset($_POST['postCOMMENT'])) {
    if ($App->container['users']->userLoggedIn()) {
        $comments = htmlentities($_POST['commento']);
        $blogid = htmlentities($_POST['post_id']);
        $App->container['blog']->addcomments($blogid, $comments);
        header("location: " . HOST . "/blog/id/" . $blogid);
        exit;
    }
}
if (isset($_POST['search'])) {

    $searchstring = $App->container['security']->mySqlSecure(htmlentities($_POST['searchform']));

    if (empty($searchstring)) {
        header("location: " . HOST . "/blog");
        exit;
    }

    header("location: " . HOST . "/blog/search/" . $searchstring . "");
    exit;
}

if (isset($_POST['save_settings_general'])) {

    if ($App->container['users']->staffLoggedIn()) {

        $user_rank = $App->container['users']->getInfo($_SESSION['staff']['id'], 'rank');
        if ($user_rank >= 3) {
            $settings_site_name = htmlentities($_POST['settings_site_name']);
            $settings_site_description = htmlentities($_POST['settings_site_description']);
            $settings_site_url = htmlentities($_POST['settings_site_url']);
            $settings_site_timezone = htmlentities($_POST['settings_site_timezone']);
            $settings_site_mainteinance = htmlentities($_POST['settings_site_mainteinance']);
            $settings_site_private = htmlentities($_POST['settings_site_private']);
            $settings_site_use_cache = htmlentities($_POST['settings_site_use_cache']);

            if (empty($settings_site_name)):
                define("INDEX_ERROR", $App->container['languages']->ea('error_page_settings_general_name', '1'));
            endif;
            if (empty($settings_site_url)):
                define("INDEX_ERROR", $App->container['languages']->ea('error_page_settings_general_url', '1'));
            endif;

            if ($App->container['settings']->save_settings('site_name', $settings_site_name) == false) {
                define("INDEX_ERROR", $App->container['languages']->ea('error_page_settings_general_save', '1'));
            };
            if ($App->container['settings']->save_settings('site_description', $settings_site_description) == false) {
                define("INDEX_ERROR", $App->container['languages']->ea('error_page_settings_general_save', '1'));
            };
            if ($App->container['settings']->save_settings('site_url', $settings_site_url) == false) {
                define("INDEX_ERROR", $App->container['languages']->ea('error_page_settings_general_save', '1'));
            };
            if ($App->container['settings']->save_settings('site_timezone', $settings_site_timezone) == false) {
                define("INDEX_ERROR", $App->container['languages']->ea('error_page_settings_general_save', '1'));
            };
            if ($App->container['settings']->save_settings('site_maintenance', $settings_site_mainteinance) == false) {
                define("INDEX_ERROR", $App->container['languages']->ea('error_page_settings_general_save', '1'));
            };
            if ($App->container['settings']->save_settings('site_use_cache', $settings_site_use_cache) == false) {
                define("INDEX_ERROR", $App->container['languages']->ea('error_page_settings_general_save', '1'));
            };
            $App->container['settings']->add_settings_value('site_private', 'false');
            if ($App->container['settings']->save_settings('site_private', $settings_site_private) == false) {
                define("INDEX_ERROR", $App->container['languages']->ea('error_page_settings_general_save', '1'));
            };

            header("location: " . HOST . "/my-admin/settings_general");
            exit;
        }
    }

}
if (isset($_POST['save_settings_blog'])) {

    if ($App->container['users']->staffLoggedIn()) {

        $user_rank = $App->container['users']->getInfo($_SESSION['staff']['id'], 'rank');
        if ($user_rank >= 3) {

            $settings_blog_private = htmlentities($_POST['settings_blog_private']);
            $settings_blog_comments_active = htmlentities($_POST['settings_blog_comments_active']);
            $settings_blog_comments_approve = htmlentities($_POST['settings_blog_comments_approve']);

            if ($App->container['settings']->save_settings('blog_private', $settings_blog_private) == false) {
                define("INDEX_ERROR", $App->container['languages']->ea('error_page_settings_general_save', '1'));
            };
            if ($App->container['settings']->save_settings('blog_comments_active', $settings_blog_comments_active) == false) {
                define("INDEX_ERROR", $App->container['languages']->ea('error_page_settings_general_save', '1'));
            };
            if ($App->container['settings']->save_settings('blog_comments_approve', $settings_blog_comments_approve) == false) {
                define("INDEX_ERROR", $App->container['languages']->ea('error_page_settings_general_save', '1'));
            };

            if ($settings_blog_private == 'true') {
                $App->container['blog']->setPrivate(true);
            } else {
                $App->container['blog']->setPrivate(false);
            }

            header("location: " . HOST . "/my-admin/settings_blog");
            exit;
        }

    }

}
if (isset($_POST['save_settings_style'])) {

    if ($App->container['users']->staffLoggedIn()) {
        $user_rank = $App->container['users']->getInfo($_SESSION['staff']['id'], 'rank');
        if ($user_rank >= 3) {
            $settings_style_language = htmlentities($_POST['settings_style_language']);
            $settings_style_template = htmlentities($_POST['settings_style_template']);
            $settings_style_template_language = htmlentities($_POST['settings_style_template_language']);

            if ($App->container['settings']->save_settings('site_language', $settings_style_language) == false) {
                define("INDEX_ERROR", $App->container['languages']->ea('error_page_settings_general_save', '1'));
            };
            if ($App->container['settings']->save_settings('site_template', $settings_style_template) == false) {
                define("INDEX_ERROR", $App->container['languages']->ea('error_page_settings_general_save', '1'));
            };
            if ($App->container['settings']->save_settings('site_template_language', $settings_style_template_language) == false) {
                define("INDEX_ERROR", $App->container['languages']->ea('error_page_settings_general_save', '1'));
            };

            header("location: " . HOST . "/my-admin/settings_style");
            exit;
        }
    }

}

if (isset($_POST['save_settings_xml_commands'])) {

    if ($App->container['users']->staffLoggedIn()) {
        $user_rank = $App->container['users']->getInfo($_SESSION['staff']['id'], 'rank');
        if ($user_rank >= 3) {
            $xml_command_code = $_POST['xml_command_code'];
            $mycms_xml = simplexml_load_string($xml_command_code);
            if (isset($mycms_xml->command['value'])) {
                if ($App->container['security']->myCmsXmlCommand($mycms_xml->command['value'])) {
                    if ($mycms_xml->command['value'] == "add_new_language") {
                        if (empty($mycms_xml->command->language_name)) {
                        } else {
                            if (empty($mycms_xml->command->language_language)) {
                            } else {
                                $App->container['database']->query("INSERT INTO my_language (language_name,language_language) VALUES (:language_name, :language_language)", ['language_name' => $mycms_xml->command->language_name, 'language_language' => $mycms_xml->command->language_language]);
                            }
                        }
                    } elseif ($mycms_xml->command['value'] == "remove_language") {
                        if (empty($mycms_xml->command->language_language)) {
                        } else {
                            $App->container['database']->query('DELETE FROM my_language WHERE language_language = :language_language LIMIT 1', ['language_language' => $mycms_xml->command->language_language]);
                        }
                    } elseif ($mycms_xml->command['value'] == "add_new_style") {
                        if (empty($mycms_xml->command->style_name)) {
                        } else {
                            $App->container['database']->query("INSERT INTO my_style (style_name) VALUES (:style_name)", ['style_name' => $mycms_xml->command->style_name]);
                        }
                    } elseif ($mycms_xml->command['value'] == "remove_style") {
                        if (empty($mycms_xml->command->style_name)) {
                        } else {
                            $App->container['database']->query('DELETE FROM my_style WHERE style_path_name = :style_path_name LIMIT 1', ['style_path_name' => $mycms_xml->command->style_path_name]);
                        }
                    }
                }
            }
        }
    }
    header("location: " . HOST . "/my-admin/xml_command");
    exit;

}

if (isset($_POST['import_page_json'])) {

    if ($App->container['users']->staffLoggedIn()) {
        $user_rank = $App->container['users']->getInfo($_SESSION['staff']['id'], 'rank');
        if ($user_rank >= 3) {
            $json_code = $_POST['json_code'];
            $mycms_json = json_decode($json_code, true);
            if ($mycms_json != false) {
                $pages_title = $App->container['functions']->addSpace(addslashes($mycms_json['pageTITLE']));
                $pages_content = addslashes($mycms_json['pageHTML']);
                $pages_menu_id = $mycms_json['pageID_MENU'];

                $page_url = $mycms_json['pageURL'];

                if (substr($pages_menu_id, 5, strlen($pages_menu_id) - 5) == $pages_title) {
                    $find_url = $App->container['database']->single("SELECT COUNT(*) FROM my_page WHERE pageURL = :simulate_url", ["simulate_url" => $page_url]);
                    if ($find_url > 0) {
                        $page_url = "{@siteURL@}/" . $App->container['security']->myGenerateRandom(5) . $pages_title;
                    }


                    $App->container['database']->query("INSERT INTO my_page (pageTITLE,pageURL,pageHTML, pageID_MENU) VALUES ('$pages_title', '$page_url', '$pages_content', '$pages_menu_id')");
                }

            }
        }
    }
    header("location: " . HOST . "/my-admin/my_page_import");
    exit;

}

if (isset($_POST['page_settings_user_save_button'])) {
    if ($App->container['users']->staffLoggedIn()) {
        $password = htmlentities($App->container['security']->mySqlSecure($_POST['password']));
        $new_password = htmlentities($App->container['security']->mySqlSecure($_POST['new_password']));
        $password_repeat = htmlentities($App->container['security']->mySqlSecure($_POST['password_repeat']));


        $validate_password = $App->container['users']->validate("password", $new_password);

        $user_data = $App->container['users']->getUserData($_SESSION['staff']['id']);

        if (!password_verify($password, $user_data['password'])) {
            define("INDEX_ERROR", $App->container['languages']->ea("error_wrong_password", '1'));

            return 0;
        } elseif ($validate_password["valid"] == 0) {
            define("INDEX_ERROR", $App->container['languages']->ea("error_wrong_new_password", '1'));

            return 0;
        } elseif ($new_password != $password_repeat) {
            define("INDEX_ERROR", $App->container['languages']->ea("error_repeat_password", '1'));

            return 0;
        }

        $password = $App->container['security']->myCmsSecurityCreatePassword($new_password);

        $App->container['database']->query("UPDATE my_users SET password = :password WHERE id = :id ", ["id" => $_SESSION['staff']['id'], "password" => $password]);

    }
}

if (isset($_POST['clear_all_caches_settings_general'])) {
    $App->container['cache']->clearAll();

}

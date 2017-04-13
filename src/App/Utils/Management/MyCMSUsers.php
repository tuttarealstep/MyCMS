<?php
    /**
     * User: tuttarealstep
     * Date: 10/04/16
     * Time: 0.46
     */

    namespace MyCMS\App\Utils\Management;

    use MyCMS\App\Utils\Exceptions\MyCMSException;

    class MyCMSUsers
    {
        private $container;

        /**
         * MyCMSUsers constructor.
         * @param $container
         */
        function __construct($container)
        {
            $this->container = $container;
        }

        /**
         * Login function for themes
         *
         * @param $email
         * @param $password
         * @param int $remember
         * @return array
         * @throws MyCMSException
         */
        public function login($email, $password, $remember = 0)
        {

            if ($this->is_user_banned()) {
                return array("login" => 0, "error" => "user_banned");
            }

            $validate_email = $this->validate("email", $email);
            $validate_password = $this->validate("password", $password);

            if ($validate_email["valid"] == 0) {
                return array("login" => 0, "error" => "error_email_password");
            } elseif ($validate_password["valid"] == 0) {
                return array("login" => 0, "error" => "error_email_password");
            }

            $user_id = $this->get_user_id($email);

            if (!$user_id) {
                return array("login" => 0, "error" => "error_email_password");
            }

            $user_data = $this->get_user_data($user_id);

            if (!password_verify($password, $user_data['password'])) {
                return array("login" => 0, "error" => "error_email_password");
            }

            $validate_cookie = $this->add_session($user_id, $remember);

            if ($validate_cookie["valid"] == false) {
                throw new MyCMSException("LOGIN SYSTEM ERROR!");
            }

            $_SESSION['user']['id'] = $user_id;
            $_SESSION['user']['hash'] = "";

            if ($validate_cookie["expire_time"] != 0) {
                $_SESSION['user']['hash'] = $validate_cookie["hash"];
                if (!isset($_COOKIE['remember_me'])) {
                    unset($_COOKIE['remember_me']);
                    setcookie('remember_me', $_SESSION['user']['hash'], $validate_cookie["expire_time"]);
                }

            }

            $data_last_access = date("Y-m-d H:i:s", time());
            $user_ip = $this->user_ip();

            $this->container['database']->query("UPDATE my_users SET ip = :ip WHERE id = :k", array("ip" => $user_ip, "k" => $user_id));
            $this->container['database']->query("UPDATE my_users SET last_access = :last_access WHERE id = :k", array("last_access" => $data_last_access, "k" => $user_id));

            $this->set_user_tag();

            return array("login" => 1, "error" => "");
        }

        /**
         * This function check if a user is banned giving an ip or check automatically if the
         * logged in user is banned.
         *
         * @param null $user_ip
         * @return bool
         */
        private function is_user_banned($user_ip = null)
        {

            if($user_ip == null)
            {
                $user_ip = $this->user_ip();
            }

            $count = $this->container['database']->rowCount("SELECT expire_date FROM my_users_banned WHERE user_ip = :ip LIMIT 1", array("ip" => $user_ip));
            if ($count == 0) {
                return false;
            }

            $expire_date = strtotime($this->container['database']->single("SELECT expire_date FROM my_users_banned WHERE user_ip = :ip LIMIT 1", array("ip" => $user_ip)));
            $current_date = strtotime(date("Y-m-d H:i:s"));

            if ($current_date < $expire_date) {
                return true;
            }

            if ($current_date > $expire_date) {
                $this->container['database']->query("DELETE FROM my_users_banned WHERE user_ip = :ip LIMIT 1", array("ip" => $user_ip));
            }

            return false;
        }

        /**
         * Return the user ip
         *
         * @return string
         */
        public function user_ip()
        {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $user_ip = $_SERVER['REMOTE_ADDR'];
            } else {
                $user_ip = "";
            }

            return $user_ip;
        }

        /**
         * This function is used for validate the user email, password name or surname.
         * @param $type
         * @param $string
         * @return array|bool
         */
        public function validate($type, $string)
        {
            switch ($type) {
                case "email":
                    if (filter_var($string, FILTER_VALIDATE_EMAIL)) {
                        return array("valid" => 1, "error" => "");
                    } else {
                        return array("valid" => 0, "error" => "invalid_email");
                    }
                    break;
                case "password":
                    if (strlen($string) < 6) {
                        return array("valid" => 0, "error" => "short_password");
                    } elseif (strlen($string) > 72) {
                        return array("valid" => 0, "error" => "long_password");
                        /* } elseif (!preg_match('@[A-Z]@', $string) || !preg_match('@[a-z]@', $string) || !preg_match('@[0-9]@', $string)) {
                             return array("valid" => 0, "error"=>"invalid_password"); */
                    } else {
                        return array("valid" => 1, "error" => "");
                    }
                    break;
                case "name":
                    if (strlen($string) < 4 || strlen($string) > 20) {
                        return array("valid" => 0, "error" => "");
                    } else {
                        return array("valid" => 1, "error" => "");
                    }
                    break;
                case "surname":
                    if (strlen($string) < 4 || strlen($string) > 20) {
                        return array("valid" => 0, "error" => "");
                    } else {
                        return array("valid" => 1, "error" => "");
                    }
                    break;
            }

            return false;

        }

        /**
         * Return the user id by giving an email
         *
         * @param $email
         * @return bool
         */
        public function get_user_id($email)
        {
            $this->container['database']->bind("mail", $email);
            $id = $this->container['database']->single("SELECT id FROM my_users WHERE mail = :mail LIMIT 1");
            if (isset($id)) {
                return $id;
            }

            return false;
        }

        /**
         * Return user data by giving the user id
         *
         * @param $user_id
         * @return bool
         */
        public function get_user_data($user_id)
        {
            $filter_id_user = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
            if (filter_var($filter_id_user, FILTER_VALIDATE_INT)) {
                $sql = $this->container['database']->row("SELECT * FROM my_users WHERE id = :user_id LIMIT 1", array("user_id" => $filter_id_user));

                return $sql;
            }

            return false;
        }

        /**
         * Make the remember me session.
         *
         * @param $user_id
         * @param $remember
         * @return array|bool
         */
        public function add_session($user_id, $remember)
        {
            $user_ip = $this->user_ip();
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
            $user_data = $this->get_user_data($user_id);
            if (!$user_data) {
                return false;
            }

            $user_cookie['hash'] = sha1($user_data['name'] . $user_data['surname'] . $user_data['mail'] . microtime());

            if ($remember == true) {
                $user_cookie['expire'] = date("Y-m-d H:i:s", strtotime("+1 month"));
                $user_cookie['expire_time'] = strtotime($user_cookie['expire']);
            } else {
                $user_cookie['expire'] = date("Y-m-d H:i:s", strtotime("+1 month"));
                $user_cookie['expire_time'] = 0;
            }
            $user_cookie['cookie_value'] = sha1($user_cookie['hash'] . SECRET_KEY);

            if ($user_cookie['expire_time'] != 0) {
                $this->container['database']->query("INSERT INTO my_security_cookie (cookie_name,cookie_value,cookie_user,cookie_expire, cookie_agent, cookie_ip) VALUES(:cookie_name, :cookie_value, :user_id, :cookie_expire, :cookie_agent, :cookie_ip)", array("cookie_name" => "remember_me", "cookie_value" => $user_cookie['cookie_value'], "user_id" => $user_id, "cookie_expire" => $user_cookie['expire_time'], "cookie_agent" => $user_agent, "cookie_ip" => $user_ip));
                $info = $this->container['database']->single("SELECT COUNT(*) FROM my_security_cookie WHERE cookie_user = :user_id_F AND cookie_name = 'remember_me' AND cookie_value=:cookie_value_F LIMIT 1", array("user_id_F" => $user_id, "cookie_value_F" => $user_cookie['cookie_value']));
                if ($info == 0) {
                    return array("valid" => false, "expire_time" => $user_cookie['expire_time']);
                }
            }

            return array("valid" => true, "expire_time" => $user_cookie['expire_time'], "hash" => $user_cookie['cookie_value']);
        }

        /**
         * This function set all the user tags
         */
        public function set_user_tag()
        {
            if (defined("LOADER_LOAD_PAGE") && LOADER_LOAD_PAGE == true) {
                if ($this->user_logged_in()) {
                    $this->container['theme']->add_tag('user_name', $this->container['security']->my_sql_secure($this->getInfo($_SESSION['user']['id'], 'name')));
                    $this->container['theme']->add_tag('user_surname', $this->container['security']->my_sql_secure($this->getInfo($_SESSION['user']['id'], 'surname')));
                    $this->container['theme']->add_tag('user_mail', $this->container['security']->my_sql_secure($this->getInfo($_SESSION['user']['id'], 'mail')));
                    $this->container['theme']->add_tag('user_ip', $this->getInfo($_SESSION['user']['id'], 'ip'));
                    $this->container['theme']->add_tag('user_rank', $this->getInfo($_SESSION['user']['id'], 'rank'));
                    $this->container['theme']->add_tag('user_last_access', $this->getInfo($_SESSION['user']['id'], 'last_access'));
                } else {
                    $this->container['theme']->add_tag('user_name', "");
                    $this->container['theme']->add_tag('user_surname', "");
                    $this->container['theme']->add_tag('user_mail', "");
                    $this->container['theme']->add_tag('user_ip', "");
                    $this->container['theme']->add_tag('user_rank', "");
                    $this->container['theme']->add_tag('user_last_access', "");
                }
                if ($this->staff_logged_in()) {
                    $this->container['theme']->add_tag('user_name', $this->container['security']->my_sql_secure($this->getInfo($_SESSION['staff']['id'], 'name')));
                    $this->container['theme']->add_tag('user_surname', $this->container['security']->my_sql_secure($this->getInfo($_SESSION['staff']['id'], 'surname')));
                    $this->container['theme']->add_tag('user_mail', $this->container['security']->my_sql_secure($this->getInfo($_SESSION['staff']['id'], 'mail')));
                    $this->container['theme']->add_tag('user_ip', $this->getInfo($_SESSION['staff']['id'], 'ip'));
                    $this->container['theme']->add_tag('user_rank', $this->getInfo($_SESSION['staff']['id'], 'rank'));
                    $this->container['theme']->add_tag('user_last_access', $this->getInfo($_SESSION['staff']['id'], 'last_access'));
                }
            }
        }

        /**
         * getInfo is used for retrieve user information from the database.
         *
         * $key is the user id
         * $string is a field in the user table
         *
         * @param $key
         * @param $string
         * @return bool
         */
        public function getInfo($key, $string)
        {
            $filter_id_user = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $filter_string = filter_var($string, FILTER_SANITIZE_STRING);
            if (filter_var($filter_id_user, FILTER_VALIDATE_INT)) {
                $sql = $this->container['database']->row("SELECT * FROM my_users WHERE id = :user_id LIMIT 1", array("user_id" => $filter_id_user));

                return $sql[ $filter_string ];
            }

            return false;
        }

        /**
         * Change user info
         *
         * @param $key
         * @param $string
         * @param $value
         * @return bool
         */
        public function setInfo($key, $string, $value)
        {
            if($string == "password" || $string == "email")
            {
             return false;
            }
            $filter_id_user = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $filter_string = filter_var($string, FILTER_SANITIZE_STRING);
            $filter_value = filter_var($value, FILTER_SANITIZE_STRING);
            if (filter_var($filter_id_user, FILTER_VALIDATE_INT)) {
                $this->container['database']->query("UPDATE my_users SET $filter_string = :svalue WHERE id = :user_id LIMIT 1", array("user_id" => $filter_id_user, "svalue" => $filter_value));
                return true;
            }

            return false;
        }

        /**
         * This is the admin login function
         *
         * @param $email
         * @param $password
         * @param int $remember
         * @return array
         * @throws MyCMSException
         */
        public function login_admin($email, $password, $remember = 0)
        {
            if ($this->is_user_banned()) {
                return array("login" => 0, "error" => "user_banned");
            }

            $validate_email = $this->validate("email", $email);
            $validate_password = $this->validate("password", $password);

            if ($validate_email["valid"] == 0) {
                return array("login" => 0, "error" => "error_email_password");
            } elseif ($validate_password["valid"] == 0) {
                return array("login" => 0, "error" => "error_email_password");
            }

            $user_id = $this->get_user_id($email);

            if (!$user_id) {
                return array("login" => 0, "error" => "error_email_password");
            }

            $user_data = $this->get_user_data($user_id);

            if (!password_verify($password, $user_data['password'])) {
                return array("login" => 0, "error" => "error_email_password");
            }

            if ($user_data['rank'] < 2) {
                return array("login" => 0, "error" => "error_email_password");
            }

            $validate_cookie = $this->add_session_admin($user_id, $remember);

            if ($validate_cookie["valid"] == false) {
                throw new MyCMSException("LOGIN SYSTEM ERROR!");
            }

            $_SESSION['staff']['id'] = $user_id;
            $_SESSION['staff']['hash'] = "";

            if ($validate_cookie["expire_time"] != 0) {
                $_SESSION['staff']['hash'] = $validate_cookie["hash"];
                if (!isset($_COOKIE['remember_me_admin'])) {
                    unset($_COOKIE['remember_me_admin']);
                    setcookie('remember_me_admin', $_SESSION['staff']['hash'], $validate_cookie["expire_time"]);
                }

            }

            $data_last_access = date("Y-m-d H:i:s", time());
            $user_ip = $this->user_ip();

            $this->container['database']->query("UPDATE my_users SET ip = :ip WHERE id = :k", array("ip" => $user_ip, "k" => $user_id));
            $this->container['database']->query("UPDATE my_users SET last_access = :last_access WHERE id = :k", array("last_access" => $data_last_access, "k" => $user_id));


            $this->set_user_tag();

            return array("login" => 1, "error" => "");
        }

        /**
         * This function add an admin remember me session.
         *
         * @param $user_id
         * @param $remember
         * @return array|bool
         */
        public function add_session_admin($user_id, $remember)
        {
            $user_ip = $this->user_ip();
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
            $user_data = $this->get_user_data($user_id);
            if (!$user_data) {
                return false;
            }

            $user_cookie['hash'] = sha1($user_data['name'] . $user_data['surname'] . $user_data['mail'] . microtime());

            if ($remember == true) {
                $user_cookie['expire'] = date("Y-m-d H:i:s", strtotime("+1 month"));
                $user_cookie['expire_time'] = strtotime($user_cookie['expire']);
            } else {
                $user_cookie['expire'] = date("Y-m-d H:i:s", strtotime("+1 month"));
                $user_cookie['expire_time'] = 0;
            }
            $user_cookie['cookie_value'] = sha1($user_cookie['hash'] . SECRET_KEY);

            if ($user_cookie['expire_time'] != 0) {
                $this->container['database']->query("INSERT INTO my_security_cookie (cookie_name,cookie_value,cookie_user,cookie_expire, cookie_agent, cookie_ip) VALUES(:cookie_name, :cookie_value, :user_id, :cookie_expire, :cookie_agent, :cookie_ip)", array("cookie_name" => "remember_me_admin", "cookie_value" => $user_cookie['cookie_value'], "user_id" => $user_id, "cookie_expire" => $user_cookie['expire_time'], "cookie_agent" => $user_agent, "cookie_ip" => $user_ip));
                $info = $this->container['database']->single("SELECT COUNT(*) FROM my_security_cookie WHERE cookie_user = :user_id_F AND cookie_name = 'remember_me_admin' AND cookie_value=:cookie_value_F LIMIT 1", array("user_id_F" => $user_id, "cookie_value_F" => $user_cookie['cookie_value']));
                if ($info == 0) {
                    return array("valid" => false, "expire_time" => $user_cookie['expire_time']);
                }
            }

            return array("valid" => true, "expire_time" => $user_cookie['expire_time'], "hash" => $user_cookie['cookie_value']);
        }

        /**
         * This function is used for register and user.
         *
         * Return an error inside an array
         * The return array have 2 keys
         * "register" => who contain an int value, 0 = fail, 1 = success
         * "error" => empty if register = 1 or with a string.
         *
         * The "error" key strings are:
         * 'error_email' => for an invalid email
         * 'error_password' => for an invalid password
         * 'error_name' => for an invalid user name
         * 'error_surname' => for an invalid user surname
         * 'error_email_in_use' => if the inserted email is in user
         *
         * @param $email
         * @param $password
         * @param $name
         * @param $surname
         * @return array
         */
        public function register($email, $password, $name, $surname)
        {
            if ($this->is_user_banned()) {
                return array("register" => 0, "error" => "user_banned");
            }

            $validate_email = $this->validate("email", $email);
            $validate_password = $this->validate("password", $password);
            $validate_name = $this->validate("name", $name);
            $validate_surname = $this->validate("surname", $surname);

            if ($validate_email["valid"] == 0) {
                return array("register" => 0, "error" => "error_email");
            } elseif ($validate_password["valid"] == 0) {
                return array("register" => 0, "error" => "error_password");
            } elseif ($validate_name["valid"] == 0) {
                return array("register" => 0, "error" => "error_name");
            } elseif ($validate_surname["valid"] == 0) {
                return array("register" => 0, "error" => "error_surname");
            } elseif ($this->control_mail($email)) {
                return array("register" => 0, "error" => "error_email_in_use");
            }

            $password = $this->container['security']->my_cms_security_create_password($password);

            $ip = $_SERVER['REMOTE_ADDR'];
            $name = filter_var($name, FILTER_SANITIZE_STRING);
            $surname = filter_var($surname, FILTER_SANITIZE_STRING);
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $this->container['database']->query("INSERT INTO my_users (name,surname,password,mail,ip,rank) VALUES(:name, :surname, :password, :email, :ip, '1')", array("name" => $name, "surname" => $surname, "password" => $password, "email" => $email, "ip" => $ip));


            return array("register" => 1, "error" => "");

        }

        /**
         * Check if an email is used
         *
         * @param $email
         * @return mixed
         */
        public function control_mail($email)
        {
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $sql = $this->container['database']->iftrue("SELECT id FROM my_users WHERE mail = :mail LIMIT 1", array("mail" => $email));

            return $sql;
        }

        public function control_session()
        {
            if (isset($_COOKIE['remember_me'])) {

                if ($this->is_user_banned()) {
                    return false;
                }

                $user_ip = $this->user_ip();
                $user_agent = $_SERVER['HTTP_USER_AGENT'];
                $cookie_hash = $_COOKIE['remember_me'];
                $info = $this->container['database']->single("SELECT COUNT(*) FROM my_security_cookie WHERE cookie_name = 'remember_me' AND cookie_value = :cookie_value", array("cookie_value" => $cookie_hash));
                if ($info == 0) {
                    unset($_COOKIE['remember_me']);
                    setcookie('remember_me', "", time() - 3600);

                    return false;
                }

                $info_data = $this->container['database']->row("SELECT * FROM my_security_cookie WHERE cookie_name = 'remember_me' AND cookie_value = :cookie_value", array("cookie_value" => $cookie_hash));
                if ($user_agent == $info_data["cookie_agent"] || $user_ip == $info_data["cookie_ip"]) {
                    $expire_date = $info_data["cookie_expire"];
                    $current_date = strtotime(date("Y-m-d H:i:s"));
                    if ($current_date > $expire_date) {
                        $this->delete_stored_cookies($info_data["cookie_user"]);

                        return false;
                    } else {
                        if (strlen($cookie_hash) != 40) {
                            return false;
                        }
                        if ($this->user_not_logged_in()) {
                            $_SESSION['user']['id'] = $info_data["cookie_user"];
                            $_SESSION['user']['hash'] = $info_data["cookie_value"];
                        }
                    }
                } else {
                    return false;
                }
            }

            return false;
        }

        private function delete_stored_cookies($user_id)
        {
            $this->container['database']->query("DELETE FROM my_security_cookie WHERE cookie_user = :user_id AND cookie_name = 'remember_me'", array("user_id" => $user_id));
            $info = $this->container['database']->single("SELECT COUNT(*) FROM my_security_cookie WHERE cookie_user = :user_id_F AND cookie_name = 'remember_me'", array("user_id_F" => $user_id));
            if ($info == 0) {
                unset($_COOKIE['remember_me']);
                setcookie('remember_me', "", time() - 3600);

                return true;
            }

            return false;
        }

        public function control_session_admin()
        {

            if (isset($_COOKIE['remember_me_admin'])) {
                if ($this->is_user_banned()) {
                    return false;
                }

                $user_ip = $this->user_ip();
                $user_agent = $_SERVER['HTTP_USER_AGENT'];
                $cookie_hash = $_COOKIE['remember_me_admin'];
                $info = $this->container['database']->single("SELECT COUNT(*) FROM my_security_cookie WHERE cookie_name = 'remember_me_admin' AND cookie_value = :cookie_value", array("cookie_value" => $cookie_hash));
                if ($info == 0)
                {
                    unset($_COOKIE['remember_me_admin']);
                    setcookie('remember_me_admin', "", time() - 3600);

                    return false;
                }

                $info_data = $this->container['database']->row("SELECT * FROM my_security_cookie WHERE cookie_name = 'remember_me_admin' AND cookie_value = :cookie_value", array("cookie_value" => $cookie_hash));
                if ($user_agent == $info_data["cookie_agent"] || $user_ip == $info_data["cookie_ip"]) {
                    $expire_date = $info_data["cookie_expire"];
                    $current_date = strtotime(date("Y-m-d H:i:s"));
                    if ($current_date > $expire_date) {
                        $this->delete_stored_cookies_admin($info_data["cookie_user"]);

                        return false;
                    } else {
                        if (strlen($cookie_hash) != 40) {
                            return false;
                        }
                        if (!$this->staff_logged_in()) {
                            $_SESSION['staff']['id'] = $info_data["cookie_user"];
                            $_SESSION['staff']['hash'] = $info_data["cookie_value"];
                        }
                    }
                } else {
                    return false;
                }
            }

            return false;
        }

        private function delete_stored_cookies_admin($user_id)
        {
            $this->container['database']->query("DELETE FROM my_security_cookie WHERE cookie_user = :user_id AND cookie_name = 'remember_me_admin'", array("user_id" => $user_id));
            $info = $this->container['database']->single("SELECT COUNT(*) FROM my_security_cookie WHERE cookie_user = :user_id_F AND cookie_name = 'remember_me_admin'", array("user_id_F" => $user_id));
            if ($info == 0) {
                unset($_COOKIE['remember_me_admin']);
                setcookie('remember_me_admin', "", time() - 3600);

                return true;
            }

            return false;
        }

        public function control_ban()
        {
            if ($this->is_user_banned()) {
                throw new MyCMSException("You Are Banned!", "Ban");
            }
        }

        public function logout($return_url = "")
        {
            if ($this->user_logged_in()) {

                $this->delete_stored_cookies($_SESSION['user']['id']);

                if (empty($return_url)) {
                    unset($_SESSION['user']);
                    session_destroy();
                    header("Location: " . HOST . "");
                } else {
                    unset($_SESSION['user']);
                    session_destroy();
                    header("Location: " . $return_url);
                }
            }
        }

        public function logout_admin()
        {
            if ($this->staff_logged_in()) {

                $this->delete_stored_cookies_admin($_SESSION['staff']['id']);

                unset($_SESSION['staff']);
                session_destroy();
                header("Location: " . HOST . "");
            }
        }

        function isStaff()
        {
            if ($this->user_logged_in()) {
                if ($this->getInfo($_SESSION['user']['id'], 'rank') >= 2) {
                    return true;
                } else {
                    return false;
                }

            } else {
                return false;
            }
        }

        function isAdmin()
        {
            if ($this->staff_logged_in()) {
                $user_rank = $this->getInfo($_SESSION['staff']['id'], 'rank');
                if ($user_rank >= 3) {
                    return true;
                } else {
                    return false;
                }
            }

            return false;
        }

        function user_logged_in()
        {

            if (isset($_SESSION['user']['id'])):
                return true;
            else:
                return false;
            endif;

        }

        function user_not_logged_in()
        {

            if (isset($_SESSION['user']['id'])):
                return false;
            else:
                return true;
            endif;

        }

        function staff_logged_in()
        {

            if (isset($_SESSION['staff']['id'])):

                return true;

            else:

                return false;

            endif;

        }


        function hide_if_logged()
        {
            if ($this->user_logged_in()) {
                header("location: " . HOST . "");
                exit;
            }

        }

        function hide_if_not_logged()
        {
            if (!$this->user_logged_in()) {
                header("location: " . HOST . "");
                exit;
            }

        }

        function hide_if_staff_logged()
        {
            if ($this->staff_logged_in()) {
                header("location: " . HOST . "");
                exit;
            }
        }

        function hide_if_staff_not_logged()
        {
            if (!$this->staff_logged_in()) {
                header("location: " . HOST . "");
                exit;
            }
        }

        /**
         * Return name and surname by id
         *
         * @param $id
         * @return string
         */
        public function getUserName($id)
        {
            return $this->getInfo($id, "name") . " " . $this->getInfo($id, "surname");
        }

        public function userIdExist($id)
        {
            if (!empty($this->getInfo($id, "id"))) {
                return true;
            }

            return false;
        }

        public function resetPassword($password, $id)
        {
            if ($this->userIdExist($id)) {
                $password = $this->container['security']->my_cms_security_create_password($password);
                $this->container['database']->query("UPDATE my_users SET password = :password WHERE id = :id ", ["id" => $id, "password" => $password]);
            }
        }
    }

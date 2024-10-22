<?php
/**
 * User: tuttarealstep
 * Date: 10/04/16
 * Time: 0.46
 */

namespace MyCMS\App\Utils\Users;

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
    public function login($email, $password, $remember = 0, $adminPanel = false)
    {

        if ($this->isUserBanned()) {
            return ["login" => 0, "error" => "user_banned"];
        }

        $validate_email = $this->validate("email", $email);
        $validate_password = $this->validate("password", $password);

        if ($validate_email["valid"] == 0) {
            return ["login" => 0, "error" => "error_email_password"];
        } elseif ($validate_password["valid"] == 0) {
            return ["login" => 0, "error" => "error_email_password"];
        }

        $user_id = $this->getUserId($email);

        if (!$user_id) {
            return ["login" => 0, "error" => "error_email_password"];
        }

        $user_data = $this->getUserData($user_id);

        if (!password_verify($password, $user_data['password'])) {
            return ["login" => 0, "error" => "error_email_password"];
        }

        $validate_cookie = $this->addSession($user_id, $remember);

        if ($validate_cookie["valid"] == false) {
            throw new MyCMSException("LOGIN SYSTEM ERROR!");
        }

        $_SESSION['user']['id'] = $user_id;
        $_SESSION['user']['rank'] = $user_data['rank'];
        $_SESSION['user']['hash'] = "";

        if ($validate_cookie["expire_time"] != 0) {
            $_SESSION['user']['hash'] = $validate_cookie["hash"];
            if (!isset($_COOKIE['remember_me'])) {
                unset($_COOKIE['remember_me']);
                setcookie('remember_me', $_SESSION['user']['hash'], $validate_cookie["expire_time"]);
            }

        }


        $data_last_access = date("Y-m-d H:i:s", time());
        $user_ip = $this->userIp();

        $this->container['database']->query("UPDATE my_users SET ip = :ip WHERE id = :k", ["ip" => $user_ip, "k" => $user_id]);
        $this->container['database']->query("UPDATE my_users SET last_access = :last_access WHERE id = :k", ["last_access" => $data_last_access, "k" => $user_id]);

        $this->setUserTag();

        if($adminPanel && $this->currentUserHasPermission("read"))
        {
            return ["login" => 0, "error" => "error_admin_permissions"];
        }

        return ["login" => 1, "error" => ""];
    }

    /**
     * This function check if a user is banned giving an ip or check automatically if the
     * logged in user is banned.
     *
     * @param null $user_ip
     * @return bool
     */
    private function isUserBanned($user_ip = null)
    {

        if ($user_ip == null) {
            $user_ip = $this->userIp();
        }

        $count = $this->container['database']->rowCount("SELECT expire_date FROM my_users_banned WHERE user_ip = :ip LIMIT 1", ["ip" => $user_ip]);
        if ($count == 0) {
            return false;
        }

        $expire_date = strtotime($this->container['database']->single("SELECT expire_date FROM my_users_banned WHERE user_ip = :ip LIMIT 1", ["ip" => $user_ip]));
        $current_date = strtotime(date("Y-m-d H:i:s"));

        if ($current_date < $expire_date) {
            return true;
        }

        if ($current_date > $expire_date) {
            $this->container['database']->query("DELETE FROM my_users_banned WHERE user_ip = :ip LIMIT 1", ["ip" => $user_ip]);
        }

        return false;
    }

    /**
     * Return the user ip
     *
     * @return string
     */
    public function userIp()
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
                    return ["valid" => 1, "error" => ""];
                } else {
                    return ["valid" => 0, "error" => "invalid_email"];
                }
                break;
            case "password":
                if (strlen($string) < 6) {
                    return ["valid" => 0, "error" => "short_password"];
                } elseif (strlen($string) > 72) {
                    return ["valid" => 0, "error" => "long_password"];
                    /* } elseif (!preg_match('@[A-Z]@', $string) || !preg_match('@[a-z]@', $string) || !preg_match('@[0-9]@', $string)) {
                         return array("valid" => 0, "error"=>"invalid_password"); */
                } else {
                    return ["valid" => 1, "error" => ""];
                }
                break;
            case "name":
                if (strlen($string) < 4 || strlen($string) > 20) {
                    return ["valid" => 0, "error" => ""];
                } else {
                    return ["valid" => 1, "error" => ""];
                }
                break;
            case "surname":
                if (strlen($string) < 4 || strlen($string) > 20) {
                    return ["valid" => 0, "error" => ""];
                } else {
                    return ["valid" => 1, "error" => ""];
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
    public function getUserId($email)
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
    public function getUserData($user_id)
    {
        $filter_id_user = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
        if (filter_var($filter_id_user, FILTER_VALIDATE_INT)) {
            $sql = $this->container['database']->row("SELECT * FROM my_users WHERE id = :user_id LIMIT 1", ["user_id" => $filter_id_user]);

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
    public function addSession($user_id, $remember)
    {
        $user_ip = $this->userIp();
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
        $user_data = $this->getUserData($user_id);
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
            $this->container['database']->query("INSERT INTO my_security_cookie (cookie_name,cookie_value,cookie_user,cookie_expire, cookie_agent, cookie_ip) VALUES(:cookie_name, :cookie_value, :user_id, :cookie_expire, :cookie_agent, :cookie_ip)", ["cookie_name" => "remember_me", "cookie_value" => $user_cookie['cookie_value'], "user_id" => $user_id, "cookie_expire" => $user_cookie['expire_time'], "cookie_agent" => $user_agent, "cookie_ip" => $user_ip]);
            $info = $this->container['database']->single("SELECT COUNT(*) FROM my_security_cookie WHERE cookie_user = :user_id_F AND cookie_name = 'remember_me' AND cookie_value=:cookie_value_F LIMIT 1", ["user_id_F" => $user_id, "cookie_value_F" => $user_cookie['cookie_value']]);
            if ($info == 0) {
                return ["valid" => false, "expire_time" => $user_cookie['expire_time']];
            }
        }

        return ["valid" => true, "expire_time" => $user_cookie['expire_time'], "hash" => $user_cookie['cookie_value']];
    }

    /**
     * This function set all the user tags
     */
    public function setUserTag()
    {
        if (defined("LOADER_LOAD_PAGE") && LOADER_LOAD_PAGE == true) {
            if ($this->userLoggedIn()) {
                $this->container['theme']->addTag('user_name', $this->container['security']->mySqlSecure($this->getInfo($_SESSION['user']['id'], 'name')));
                $this->container['theme']->addTag('user_surname', $this->container['security']->mySqlSecure($this->getInfo($_SESSION['user']['id'], 'surname')));
                $this->container['theme']->addTag('user_mail', $this->container['security']->mySqlSecure($this->getInfo($_SESSION['user']['id'], 'mail')));
                $this->container['theme']->addTag('user_ip', $this->getInfo($_SESSION['user']['id'], 'ip'));
                $this->container['theme']->addTag('user_rank', $this->getInfo($_SESSION['user']['id'], 'rank'));
                $this->container['theme']->addTag('user_last_access', $this->getInfo($_SESSION['user']['id'], 'last_access'));
            } else {
                $this->container['theme']->addTag('user_name', "");
                $this->container['theme']->addTag('user_surname', "");
                $this->container['theme']->addTag('user_mail', "");
                $this->container['theme']->addTag('user_ip', "");
                $this->container['theme']->addTag('user_rank', "");
                $this->container['theme']->addTag('user_last_access', "");
            }
        }
    }

    function userLoggedIn()
    {
        if (isset($_SESSION['user']['id'])):
            return true;
        else:
            return false;
        endif;
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
            $sql = $this->container['database']->row("SELECT * FROM my_users WHERE id = :user_id LIMIT 1", ["user_id" => $filter_id_user]);

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
        if ($string == "password" || $string == "email") {
            return false;
        }
        $filter_id_user = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
        $filter_string = filter_var($string, FILTER_SANITIZE_STRING);
        $filter_value = filter_var($value, FILTER_SANITIZE_STRING);
        if (filter_var($filter_id_user, FILTER_VALIDATE_INT)) {
            $this->container['database']->query("UPDATE my_users SET $filter_string = :svalue WHERE id = :user_id LIMIT 1", ["user_id" => $filter_id_user, "svalue" => $filter_value]);

            return true;
        }

        return false;
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
        if ($this->isUserBanned()) {
            return ["register" => 0, "error" => "user_banned"];
        }

        $validate_email = $this->validate("email", $email);
        $validate_password = $this->validate("password", $password);
        $validate_name = $this->validate("name", $name);
        $validate_surname = $this->validate("surname", $surname);

        if ($validate_email["valid"] == 0) {
            return ["register" => 0, "error" => "error_email"];
        } elseif ($validate_password["valid"] == 0) {
            return ["register" => 0, "error" => "error_password"];
        } elseif ($validate_name["valid"] == 0) {
            return ["register" => 0, "error" => "error_name"];
        } elseif ($validate_surname["valid"] == 0) {
            return ["register" => 0, "error" => "error_surname"];
        } elseif ($this->controlMail($email)) {
            return ["register" => 0, "error" => "error_email_in_use"];
        }

        $password = $this->container['security']->myCmsSecurityCreatePassword($password);

        $ip = $_SERVER['REMOTE_ADDR'];
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $surname = filter_var($surname, FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $this->container['database']->query("INSERT INTO my_users (name,surname,password,mail,ip,rank) VALUES(:name, :surname, :password, :email, :ip, 'user')", ["name" => $name, "surname" => $surname, "password" => $password, "email" => $email, "ip" => $ip]);


        return ["register" => 1, "error" => ""];

    }

    /**
     * Check if an email is used
     *
     * @param $email
     * @return mixed
     */
    public function controlMail($email)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $sql = $this->container['database']->iftrue("SELECT id FROM my_users WHERE mail = :mail LIMIT 1", ["mail" => $email]);

        return $sql;
    }

    public function controlSession()
    {
        if (isset($_COOKIE['remember_me'])) {

            if ($this->isUserBanned()) {
                return false;
            }

            $user_ip = $this->userIp();
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $cookie_hash = $_COOKIE['remember_me'];
            $info = $this->container['database']->single("SELECT COUNT(*) FROM my_security_cookie WHERE cookie_name = 'remember_me' AND cookie_value = :cookie_value", ["cookie_value" => $cookie_hash]);
            if ($info == 0) {
                unset($_COOKIE['remember_me']);
                setcookie('remember_me', "", time() - 3600);

                return false;
            }

            $info_data = $this->container['database']->row("SELECT * FROM my_security_cookie WHERE cookie_name = 'remember_me' AND cookie_value = :cookie_value", ["cookie_value" => $cookie_hash]);
            if ($user_agent == $info_data["cookie_agent"] || $user_ip == $info_data["cookie_ip"]) {
                $expire_date = $info_data["cookie_expire"];
                $current_date = strtotime(date("Y-m-d H:i:s"));
                if ($current_date > $expire_date) {
                    $this->deleteStoredCookies($info_data["cookie_user"]);

                    return false;
                } else {
                    if (strlen($cookie_hash) != 40) {
                        return false;
                    }
                    if ($this->userNotLoggedIn()) {
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

    private function deleteStoredCookies($user_id)
    {
        $this->container['database']->query("DELETE FROM my_security_cookie WHERE cookie_user = :user_id AND cookie_name = 'remember_me'", ["user_id" => $user_id]);
        $info = $this->container['database']->single("SELECT COUNT(*) FROM my_security_cookie WHERE cookie_user = :user_id_F AND cookie_name = 'remember_me'", ["user_id_F" => $user_id]);
        if ($info == 0) {
            unset($_COOKIE['remember_me']);
            setcookie('remember_me', "", time() - 3600);

            return true;
        }

        return false;
    }

    function userNotLoggedIn()
    {

        if (isset($_SESSION['user']['id'])):
            return false;
        else:
            return true;
        endif;

    }

    public function controlBan()
    {
        if ($this->isUserBanned()) {
            throw new MyCMSException("You Are Banned!", "Ban");
        }
    }

    public function logout($return_url = "")
    {
        if ($this->userLoggedIn()) {

            $this->deleteStoredCookies($_SESSION['user']['id']);

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

    function hideIfLogged()
    {
        if ($this->userLoggedIn()) {
            header("location: " . HOST . "");
            exit;
        }
    }

    function hideIfNotLogged()
    {
        if (!$this->userLoggedIn()) {
            header("location: " . HOST . "");
            exit;
        }
    }

    /**
     * @deprecated
     */
    function hideIfStaffLogged()
    {
        if ($this->userLoggedIn()) {
            header("location: " . HOST . "");
            exit;
        }
    }

    /**
     * @deprecated
     */
    function hideIfStaffNotLogged()
    {
        if (!$this->userLoggedIn()) {
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
        return $this->container['plugins']->applyEvent('getUserName', $id);
    }

    /**
     * Hook function
     * @param $id
     * @return string
     */
    private function getUserNameEvent($id)
    {
        return $this->getInfo($id, "name") . " " . $this->getInfo($id, "surname");
    }

    public function resetPassword($password, $id)
    {
        if ($this->userIdExist($id)) {
            $password = $this->container['security']->myCmsSecurityCreatePassword($password);
            $this->container['database']->query("UPDATE my_users SET password = :password WHERE id = :id ", ["id" => $id, "password" => $password]);
        }
    }

    public function userIdExist($id)
    {
        if (!empty($this->getInfo($id, "id"))) {
            return true;
        }

        return false;
    }

    /**
     * @param $permission
     * @return bool
     * @see currentUserHasPermission
     */
    public function hasPermission($permission)
    {
        return $this->currentUserHasPermission($permission);
    }

 /*
    //todo bug check event
    public function currentUserHasPermission($permission)
    {
        var_dump($this->container['plugins']->applyEvent('currentUserHasPermissionEvent', $permission));
        die("sda ");
        return $this->container['plugins']->applyEvent('currentUserHasPermissionEvent', $permission);
    }*/

    /**
     * @param $permission
     * @return bool
     */
    public function currentUserHasPermission($permission)
    {
        //todo map permissions for other check for editors ecc
        if($this->userLoggedIn())
        {
            $user_rank = $this->getInfo($_SESSION['user']['id'], 'rank');
            $role = $this->container['roles']->getRole($user_rank);
            if($role == null)
                return false;

            return $role->hasPermission($permission);
        }

        return false;
    }

    /**
     * getData is used for retrieve user custom information from the database.
     *
     * $key is the user id
     * $string is a field in the user table
     *
     * @param $user_id
     * @param $key
     * @return bool
     */
    public function getData($user_id, $key)
    {
        return $this->container['plugins']->applyEvent('getUserData', $user_id, $key);
    }

    /**
     * getData is used for retrieve user custom information from the database.
     *
     * $key is the user id
     * $string is a field in the user table
     *
     * @param $user_id
     * @param $key
     * @return bool
     */
    public function getDataEvent($user_id, $key)
    {
        $filter_id_user = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
        $filter_string = filter_var($key, FILTER_SANITIZE_STRING);
        if (filter_var($filter_id_user, FILTER_VALIDATE_INT))
        {
            $sql = $this->container['database']->single("SELECT data_value FROM my_users_data WHERE user_id = :user_id AND data_key = :data_key LIMIT 1", ["user_id" => $filter_id_user, "data_key" => $filter_string]);
            return $sql;
        }

        return false;
    }

    /**
     * Change user info
     *
     * @param $user_id
     * @param $key
     * @param $value
     * @return bool
     */
    public function setData($user_id, $key, $value)
    {
        $filter_id_user = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
        $filter_key = filter_var($key, FILTER_SANITIZE_STRING);
        $filter_value = filter_var($value, FILTER_SANITIZE_STRING);

        if($this->container['database']->single("SELECT data_key FROM my_users_data WHERE user_id = :user_id AND data_key = :data_key LIMIT 1", ["user_id" => $filter_id_user, "data_key" => $filter_key]))
        {
            if (filter_var($filter_id_user, FILTER_VALIDATE_INT)) {
                $this->container['database']->query("UPDATE my_users_data SET data_value = :data_value WHERE user_id = :user_id AND data_key = :data_key  LIMIT 1", ["user_id" => $filter_id_user, "data_value" => $filter_value, "data_key" => $filter_key]);

                return true;
            }
        } else {
            if (filter_var($filter_id_user, FILTER_VALIDATE_INT)) {
                $this->container['database']->query("INSERT INTO my_users_data (user_id, data_key, data_value) VALUES (:user_id, :data_key, :data_value)", ["user_id" => $filter_id_user, "data_value" => $filter_value, "data_key" => $filter_key]);

                return true;
            }
        }

        return false;
    }

    public function setEvents()
    {
        $this->container['plugins']->addEvent('getUserData', [$this, 'getDataEvent'], 1, 2);

        $this->container['plugins']->addEvent('getUserName', [$this, 'getUsernameEvent']);
    }
}

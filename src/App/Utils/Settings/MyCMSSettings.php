<?php
    /**
     * User: tuttarealstep
     * Date: 09/04/16
     * Time: 18.13
     */

    namespace MyCMS\App\Utils\Settings;

    use MyCMS\App\Utils\Exceptions\MyCMSException;

    class MyCMSSettings
    {
        private $database;
        private $logger;

        function __construct($database, $logger)
        {
            $this->database = $database;
            $this->logger = $logger;
        }

        function get_settings_id($name)
        {
            $filter_name = filter_var($name, FILTER_SANITIZE_STRING);
            $this->database->bind("settings_name", $filter_name);
            $information = $this->database->single("SELECT id FROM my_cms_settings WHERE settings_name = :settings_name LIMIT 1");

            return $information;
        }

        function get_settings_name($id)
        {
            $filter_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            if (filter_var($filter_id, FILTER_VALIDATE_INT)) {
                $this->database->bind("settings_id", $filter_id);
                $information = $this->database->single("SELECT settings_name FROM my_cms_settings WHERE settings_id = :settings_id LIMIT 1");

                return $information;
            } else {
                $this->logger->addInfo('Error in get_settings_name() invalid $filter_id - MyCMSSettings');
                throw new MyCMSException('Error in get_settings_name() invalid $filter_id');
            }

        }

        function get_settings_value($setting_name = "")
        {
            $setting_name = filter_var($setting_name, FILTER_SANITIZE_STRING);
            $this->database->bind("filter_setting_name", $setting_name);
            $information = $this->database->single("SELECT settings_value FROM my_cms_settings WHERE settings_name = :filter_setting_name LIMIT 1");
            if (!empty($information)) {
                return $information;
            }

            return false;
        }

        function add_settings_value($settings_name = "", $settings_value = "")
        {
            $settings_name = filter_var($settings_name, FILTER_SANITIZE_STRING);
            $settings_value = filter_var($settings_value, FILTER_SANITIZE_STRING);
            $this->database->bind("filter_settings_name", $settings_name);
            $check = $this->database->single("SELECT COUNT(*) FROM my_cms_settings WHERE settings_name = :filter_settings_name LIMIT 1");
            if ($check <= 0) {
                $this->database->query("INSERT INTO my_cms_settings(settings_name,settings_value) VALUES(:setting_name_new,:setting_value_new)", array("setting_name_new" => $settings_name, "setting_value_new" => $settings_value));

                return true;
            } else {
                return false;
            }

        }

        function save_settings($settings_name = "", $settings_value = "")
        {
            $settings_name = filter_var($settings_name, FILTER_SANITIZE_STRING);
            $settings_value = filter_var($settings_value, FILTER_SANITIZE_STRING);
            $check = $this->database->single("SELECT COUNT(*) FROM my_cms_settings WHERE settings_name = :filter_settings_name", array("filter_settings_name" => $settings_name));

            if ($check > 0) {
                $insert = $this->database->query("UPDATE my_cms_settings SET settings_value = :setting_value_new WHERE settings_name = :setting_name_new", array("setting_value_new" => $settings_value, "setting_name_new" => $settings_name));
            } else {
                return false;
            }

            if ($insert > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

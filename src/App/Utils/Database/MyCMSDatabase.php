<?php
/**
 * User: tuttarealstep
 * Date: 09/04/16
 * Time: 16.04
 */


// Grazie a : Vivek Wicky Aswal. (https://twitter.com/#!/VivekWickyAswal)
// Classe basata su https://github.com/indieteq/PHP-MySQL-PDO-Database-Class

namespace MyCMS\App\Utils\Database;

use MyCMS\App\Utils\Exceptions\MyCMSException;
use PDO;
use PDOException;

class MyCMSDatabase
{
    private $pdo;
    private $pdo_query;
    private $pdo_connection = false;
    private $parameters = [];
    private $logger;

    function __construct($logger)
    {
        $this->logger = $logger;
    }

    function disconnect()
    {
        $this->pdo = null;
    }

    public function query($query, $params = null, $fetch_mode = PDO::FETCH_ASSOC)
    {
        $query = trim(str_replace("\r", " ", $query));
        $this->init($query, $params);
        $rawStatement = explode(" ", preg_replace("/\\s+|\t+|\n+/", " ", $query));

        $statement = strtolower($rawStatement[0]);
        if ($statement === 'select' || $statement === 'show') {
            return $this->pdo_query->fetchAll($fetch_mode);
        } elseif ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
            return $this->pdo_query->rowCount();
        } else {
            return null;
        }
    }

    private function init($query, $parameters = "")
    {
        if (!$this->pdo_connection) {
            $this->connect();
        }
        try {
            $this->pdo_query = $this->pdo->prepare($query);
            $this->bindMore($parameters);

            $this->pdoBindArray($parameters);
            if (!empty($this->parameters)) {
                foreach ($this->parameters as $param) {
                    $parameters = explode("\x7F", $param);
                    $this->pdo_query->bindParam($parameters[0], $parameters[1]);
                }
            }
            $this->pdo_query->execute();

        } catch (PDOException $e) {
            $this->saveLog($e->getMessage(), $query);
            throw new MyCMSException($e->getMessage());
        }

        $this->parameters = [];
    }

    function connect()
    {
        $PDO_dsn = "mysql:host=" . C_HOST . ";dbname=" . C_DATABASE . ";";
        try {
            $PDO_user = C_USER;
            $PDO_password = C_PASSWORD;
            $this->pdo = new PDO($PDO_dsn, $PDO_user, $PDO_password, [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->pdo_connection = true;
        } catch (PDOException $e) {
            if (defined("LOADER_LOAD_PAGE") && LOADER_LOAD_PAGE == true) {
                throw new MyCMSException($e->getMessage());
            } else {
                throw new \Exception($e->getMessage());
            }
        }
    }

    public function bindMore($parray)
    {
        if (empty($this->parameters) && is_array($parray)) {
            $columns = array_keys($parray);
            foreach ($columns as $i => &$column) {
                $this->bind($column, $parray[ $column ]);
            }
        }
    }

    public function bind($parameters_add, $value)
    {
        $this->parameters[ sizeof($this->parameters) ] = ":" . $parameters_add . "\x7F" . $value;
    }

    public function pdoBindArray($parameters_array)
    {
        if (empty($this->parameters) && is_array($parameters_array)) {
            $columns = array_keys($parameters_array);
            foreach ($columns as $i => &$column) {
                $this->bind($column, $parameters_array[ $column ]);
            }
        }
    }

    private function saveLog($msg, $query = "")
    {
        $this->logger->addInfo($msg . " Query: " . $query);
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function executeTransaction()
    {
        return $this->pdo->commit();
    }

    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    public function rowCount($query, $params = null)
    {
        $this->init($query, $params);

        return $this->pdo_query->rowCount();
    }

    public function column($query, $params = null)
    {
        $this->init($query, $params);
        $Columns = $this->pdo_query->fetchAll(PDO::FETCH_NUM);
        $column = null;
        foreach ($Columns as $cells) {
            $column[] = $cells[0];
        }

        return $column;
    }

    public function row($query, $params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $this->init($query, $params);
        $result = $this->pdo_query->fetch($fetchmode);
        $this->pdo_query->closeCursor();

        return $result;
    }

    public function iftrue($query, $params = null)
    {
        if (!empty($query)) {
            if (!empty($params)) {
                $controllo = $this->single($query, $params);
                if ($controllo >= 1) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $this->saveLog("Error Empty Params (iftrue function)", $query);
            }
        } else {
            $this->saveLog("Empty Query (iftrue function)", "");
        }

        return false;
    }

    public function single($query, $params = null)
    {
        $this->init($query, $params);
        $result = $this->pdo_query->fetchColumn();
        $this->pdo_query->closeCursor();

        return $result;
    }
}

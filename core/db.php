<?php

namespace core;

use PDO;

class DB
{
    private $connection;
    private static $_instance;
    private $host = "localhost";
    private $username = "appuser";
    private $password = "AngelEmpire123";
    private $database = "test_short";

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    // Constructor
    private function __construct()
    {
        $this->connection = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database, $this->username, $this->password);
        $this->connection->exec("set names utf8");
    }

    private function __clone()
    {
    }

    public function getConnection()
    {
        return $this->connection;
    }

    private function __wakeup()
    {
    }
}

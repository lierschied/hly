<?php

namespace Core\Database;

use Core\Env;
use PDO;
use PDOException;

class Database
{
    public PDO $connection;
    protected static self $instance;

    protected function __construct()
    {
        $servername = Env::get('DB_HOST', 'mysql');
        $username = Env::get('DB_USER', 'root');
        $password = Env::get('DB_PASSWORD', 'password');
        $dbName= Env::get('DB_NAME', 'hly');

        try {
            $this->connection = new PDO("mysql:host=$servername;dbname=$dbName", $username, $password);
            // set the PDO error mode to exception
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public static function getInstance(): self
    {
        return self::$instance ?? self::$instance = new Database();
    }
}
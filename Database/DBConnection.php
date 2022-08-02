<?php

namespace Database;

use PDO;
use PDOException;

class DBConnection
{
    private $db = null;
    private $defaultConnfig = [];

    public function __construct()
    {
        $this->defaultConnfig = include $_SERVER['DOCUMENT_ROOT'] . '/Config/DBConfig.php';
    }

    public static function connect(
        string $dbhost = null,
        string $dbname = null,
        string $username = null,
        string $password = null
    ) {
        $self = new static;

        $conf = $self->defaultConnfig;

        try {
            $self->db = new PDO("mysql:host=" . ($dbhost ?? $conf['db_host']) . ";dbname=" . ($dbname ?? $conf['db_name']) . "", $username ?? $conf['db_username'], $password ?? $conf['db_password']);
            $self->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $self->db;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            die();
        }
    }
}

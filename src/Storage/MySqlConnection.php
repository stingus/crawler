<?php

namespace Stingus\Crawler\Storage;

/**
 * Class MySqlConnection
 *
 * @package Stingus\Crawler\Storage
 */
class MySqlConnection
{
    /** @var \PDO */
    private $dbh;

    /** @var string */
    private $dbName;

    /**
     * MySqlStorage constructor.
     *
     * @param array $config Connection configuration
     */
    public function __construct(array $config)
    {
        try {
            $dsn = sprintf('mysql:dbname=%s;host=%s', $config['db'], $config['host']);
            $this->dbh = new \PDO(
                $dsn,
                $config['user'],
                $config['password'],
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
            $this->dbName = $config['db'];
        } catch (\PDOException $e) {
            echo 'MySQL connection failed: ' . $e->getMessage();
        }
    }

    /**
     * @return \PDO
     */
    public function getDbh()
    {
        return $this->dbh;
    }

    /**
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }
}

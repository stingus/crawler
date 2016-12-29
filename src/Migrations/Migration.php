<?php

namespace Stingus\Crawler\Migrations;

use Stingus\Crawler\Storage\Exchange\ExchangeStorage;
use Stingus\Crawler\Storage\MySqlConnection;

/**
 * Class Migration.
 * Creates and keeps the DB schema valid
 *
 * @package Stingus\Crawler\Migrations
 */
class Migration
{
    /** Table holding the version */
    const TABLE = 'version';

    /** Last schema version */
    const LAST_VERSION = 0;

    /** @var MySqlConnection */
    private $conn;

    /**
     * Migration constructor.
     *
     * @param MySqlConnection $conn
     */
    public function __construct(MySqlConnection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Run migrations
     *
     * @uses migration0
     * @throws \RuntimeException
     */
    public function migrate()
    {
        $dbh = $this->conn->getDbh();
        $healthStatus = $this->checkHealth();
        $version = call_user_func_array([$this, 'getFromVersion'], $healthStatus);
        $dbh->beginTransaction();
        for ($i = $version + 1; $i <= self::LAST_VERSION; $i++) {
            try {
                $this->execute($i);
            } catch (\PDOException $e) {
                $dbh->rollBack();
                throw new \RuntimeException(sprintf('Error executing migrations: %s', $e->getMessage()));
            }
        }
        $dbh->commit();

        return $version;
    }

    /**
     * Check tables health
     *
     * @return array
     * @throws \RuntimeException
     */
    private function checkHealth()
    {
        $tableExists = $this->checkTablesExist();
        $versionTableExists = $this->checkVersionTablesExist();
        if ($tableExists && !$versionTableExists) {
            throw new \RuntimeException('Cannot run migration, required tables exist but the version table is missing');
        }
        if (!$tableExists && $versionTableExists) {
            $this->conn->getDbh()->exec('DROP TABLE ' . self::TABLE);
            $versionTableExists = false;
        }

        return [$tableExists, $versionTableExists];
    }

    /**
     * Get the "from" version
     *
     * @param bool $tableExists
     * @param bool $versionTableExists
     *
     * @return int|null
     * @throws \RuntimeException
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getFromVersion($tableExists, $versionTableExists)
    {
        $version = null;
        if ($versionTableExists) {
            $version = $this->getCurrentVersion();
            if (null === $version && $tableExists) {
                throw new \RuntimeException('Cannot run migration, current version is missing');
            }
            if ($version > self::LAST_VERSION) {
                throw new \RuntimeException('Cannot run migration, current version is inconsistent');
            }
        }

        return null === $version ? -1 : $version;
    }

    /**
     * Check if the required tables exist
     *
     * @return bool
     */
    private function checkTablesExist()
    {
        $dbh = $this->conn->getDbh();
        $tableNames = $this->getTableNames();
        $query = '
          SELECT COUNT(TABLE_NAME)
          FROM information_schema.TABLES
          WHERE TABLE_SCHEMA=:db_name AND TABLE_NAME IN (:table_names)
          ';
        $sth = $dbh->prepare($query);
        $sth->bindValue(':db_name', $this->conn->getDbName());
        $sth->bindValue(':table_names', implode(',', $tableNames));
        $sth->execute();

        return count($tableNames) === (int)$sth->fetchColumn(0);
    }

    /**
     * Check if the version table exist
     *
     * @return bool
     */
    private function checkVersionTablesExist()
    {
        $dbh = $this->conn->getDbh();
        $query = '
          SELECT COUNT(TABLE_NAME)
          FROM information_schema.TABLES
          WHERE TABLE_SCHEMA=:db_name AND TABLE_NAME=:table_name
          ';
        $sth = $dbh->prepare($query);
        $sth->bindValue(':db_name', $this->conn->getDbName());
        $sth->bindValue(':table_name', self::TABLE);
        $sth->execute();

        return 1 === (int)$sth->fetchColumn(0);
    }

    /**
     * Get the table names used in all storage classes
     *
     * @return array
     */
    private function getTableNames()
    {
        return [
            ExchangeStorage::TABLE,
        ];
    }

    /**
     * Get current version
     *
     * @return int|null
     */
    private function getCurrentVersion()
    {
        /** @noinspection SqlResolve */
        $sth = $this->conn->getDbh()->query('SELECT version FROM ' . self::TABLE);
        $result = $sth->fetchColumn(0);

        return false === $result ? null : (int)$result;
    }

    /**
     * Execute a version migration
     *
     * @param $version
     *
     * @throws \RuntimeException
     */
    private function execute($version)
    {
        $callable = [$this, 'migration' . $version];
        if (!is_callable($callable)) {
            throw new \RuntimeException(sprintf('Cannot run migration version %s', $version));
        }
        $callable();

        $sth = $this->conn->getDbh()->prepare('UPDATE ' . self::TABLE . ' SET version=:version');
        $sth->bindValue(':version', $version);
        $sth->execute();
    }

    /**
     * Migration 0. Schema create
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @used-by migrate()
     */
    private function migration0()
    {
        $dbh = $this->conn->getDbh();
        $dbh->exec(
            'CREATE TABLE `exchange` (
              `date` DATE NOT NULL,
              `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `aed` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `aud` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `bgn` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `brl` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `cad` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `chf` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `cny` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `czk` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `dkk` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `egp` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `eur` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `gbp` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `hrk` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `huf` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `inr` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `jpy` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `krw` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `mdl` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `mxn` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `nok` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `nzd` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `pln` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `rsd` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `rub` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `sek` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `try` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `uah` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `usd` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `xau` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `xdr` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `zar` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              `inforeuro` FLOAT(8,4) NOT NULL DEFAULT \'0.0000\',
              PRIMARY KEY (`date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
        );

        $dbh->exec(
            'CREATE TABLE `version` (
              `version` SMALLINT(5) UNSIGNED NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
        );

        $dbh->exec('INSERT INTO ' . self::TABLE . ' VALUES(0)');
    }
}

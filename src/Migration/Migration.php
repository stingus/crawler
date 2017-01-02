<?php

namespace Stingus\Crawler\Migration;

use Stingus\Crawler\Storage\MySqlConnection;

/**
 * Class Migration.
 * Creates and keeps the DB schema valid
 *
 * @package Stingus\Crawler\Migration
 */
class Migration
{
    /** Table holding the version */
    const TABLE = 'version';

    const MIGRATIONS_DIR = '../../config/migrations';

    /** @var MySqlConnection */
    private $conn;

    /** @var int */
    private $maxVersion;

    /**
     * Migration constructor.
     *
     * @param MySqlConnection $conn
     *
     * @throws \RuntimeException
     */
    public function __construct(MySqlConnection $conn)
    {
        $this->conn = $conn;
        $this->maxVersion = $this->computeMaxVersion();
    }

    /**
     * Run migrations
     *
     * @throws \RuntimeException
     */
    public function migrate()
    {
        $dbh = $this->conn->getDbh();
        $version = $this->getFromVersion();
        $dbh->beginTransaction();
        for ($i = $version + 1; $i <= $this->maxVersion; $i++) {
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
     * @return int
     */
    public function getMaxVersion()
    {
        return $this->maxVersion;
    }

    /**
     * Get the "from" version
     *
     * @return int|null
     * @throws \RuntimeException
     */
    private function getFromVersion()
    {
        $version = null;
        if ($this->hasVersionTable()) {
            $version = $this->getCurrentVersion();
            if ($version > $this->maxVersion) {
                throw new \RuntimeException('Cannot run migration, current version is inconsistent');
            }
        }

        return null === $version ? -1 : $version;
    }

    /**
     * Check if the version table exist
     *
     * @return bool
     */
    private function hasVersionTable()
    {
        $dbh = $this->conn->getDbh();
        $query = '
          SELECT COUNT(TABLE_NAME)
          FROM information_schema.TABLES
          WHERE TABLE_SCHEMA=:db_name AND TABLE_NAME=:table_name
          ';
        $sth = $dbh->prepare($query);
        $sth->bindValue(':db_name', $this->conn->getDbName());
        $sth->bindValue(':table_name', static::TABLE);
        $sth->execute();

        return 1 === (int)$sth->fetchColumn(0);
    }

    /**
     * Get current version
     *
     * @return int|null
     */
    private function getCurrentVersion()
    {
        /** @noinspection SqlResolve */
        $sth = $this->conn->getDbh()->query('SELECT version FROM ' . static::TABLE);
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
        if (false === $migration = file_get_contents(__DIR__ . '/../../config/migrations/' . $version . '.sql')) {
            throw new \RuntimeException(sprintf('Migration file %s cannot be read', $version));
        }
        $dbh = $this->conn->getDbh();
        $dbh->exec($migration);

        $query = 'UPDATE ' . static::TABLE . ' SET version=:version';
        if (0 === $version) {
            $query = 'INSERT INTO ' . static::TABLE . ' VALUES (:version)';
        }
        $sth = $dbh->prepare($query);
        $sth->bindValue(':version', $version);
        $sth->execute();
    }

    /**
     * Compute max migration version.
     * Scan the migrations directory for .sql files with integer names
     *
     * @return int
     * @throws \RuntimeException
     */
    private function computeMaxVersion()
    {
        $maxVersion = ~PHP_INT_MAX;
        $dir = new \DirectoryIterator(__DIR__ . '/' . static::MIGRATIONS_DIR);
        foreach ($dir as $fileinfo) {
            if ($fileinfo->isFile()) {
                $basename = $fileinfo->getBasename('.sql');
                /** @noinspection NotOptimalIfConditionsInspection */
                if (preg_match('/\d+/', $basename) && $maxVersion < ($basename = (int)$basename)) {
                    $maxVersion = $basename;
                }
            }
        }

        if ($maxVersion < 0) {
            throw new \RuntimeException('At least one migration file is needed (schema create)!');
        }

        return $maxVersion;
    }
}

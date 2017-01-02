<?php

namespace Stingus\Crawler\Storage\Exchange;

use Stingus\Crawler\Storage\MySqlConnection;
use Stingus\Crawler\Storage\StorageInterface;

/**
 * Class ExchangeStorage.
 * Persist exchange data in MySQL
 *
 * @package Stingus\Crawler\Storage\Exchange
 */
class ExchangeStorage implements StorageInterface
{
    /** Table storing exchange values */
    const TABLE = 'exchange';

    /** @var MySqlConnection */
    private $conn;

    /** @var \DateTime */
    private $date;

    /**
     * ExchangeStorage constructor.
     *
     * @param \DateTime       $date  Exchange rates date
     * @param MySqlConnection $conn  MySQL connection
     */
    public function __construct(\DateTime $date, MySqlConnection $conn)
    {
        $this->conn = $conn;
        $this->date = $date;
    }

    /**
     * @inheritDoc
     * @throws \PDOException
     */
    public function save(\ArrayObject $results)
    {
        $dbh = $this->conn->getDbh();
        if ($this->hasDate()) {
            return false;
        }
        $columns = array_map('strtolower', array_keys($results->getArrayCopy()));
        $columns[] = 'date';
        $placeholders = $this->conn->createPlaceholders($columns);
        $query = 'INSERT INTO ' . self::TABLE. ' (' . implode($columns, ',') . ') VALUES (' . $placeholders . ')';
        $sth = $dbh->prepare($query);

        foreach ($results as $key => $value) {
            $sth->bindValue(':' . strtolower($key), $value);
        }
        $sth->bindValue(':date', $this->date->format('Y-m-d'));

        $dbh->beginTransaction();
        try {
            $sth->execute();
        } catch (\PDOException $e) {
            $dbh->rollBack();
            throw $e;
        }
        $dbh->commit();

        return true;
    }

    /**
     * Check if date is already persisted
     *
     * @return bool
     */
    private function hasDate()
    {
        /** @noinspection SqlResolve */
        $sth = $this->conn->getDbh()->prepare('SELECT * FROM ' . self::TABLE . ' WHERE `date`=:date');
        $sth->bindValue(':date', $this->date->format('Y-m-d'));
        $sth->execute();

        return 0 !== $sth->rowCount();
    }
}

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
        if ($this->hasDate()) {
            return false;
        }
        $columns = array_map('strtolower', array_keys($results->getArrayCopy()));
        $columns[] = 'date';
        $placeholders = implode(
            array_map(
                function ($item) {
                    return ':' . $item;
                },
                $columns
            ),
            ','
        );
        $query = 'INSERT INTO ' . self::TABLE. ' (' . implode($columns, ',') . ') VALUES (' . $placeholders . ')';
        $sth = $this->conn->getDbh()->prepare($query);

        foreach ($results as $abbr => $value) {
            $sth->bindValue(':' . strtolower($abbr), $value);
        }
        $sth->bindValue(':date', $this->date->format('Y-m-d'));

        if (false === $sth->execute()) {
            throw new \PDOException('Failed to save exchange data: ' . $sth->errorInfo()[2]);
        }

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

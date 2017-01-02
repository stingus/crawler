<?php

namespace Stingus\Crawler\Storage\Weather;

use Stingus\Crawler\Storage\MySqlConnection;
use Stingus\Crawler\Storage\StorageInterface;

/**
 * Class WeatherStorage.
 * Persist weather data in MySQL
 *
 * @package Stingus\Crawler\Storage\Weather
 */
class WeatherStorage implements StorageInterface
{
    /** Table storing weather values */
    const TABLE = 'weather';

    /** @var MySqlConnection */
    private $conn;

    /**
     * WeatherStorage constructor.
     *
     * @param MySqlConnection $conn  MySQL connection
     */
    public function __construct(MySqlConnection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @inheritDoc
     * @throws \PDOException
     */
    public function save(\ArrayObject $results)
    {
        $dbh = $this->conn->getDbh();
        $dbh->beginTransaction();
        /** @var \ArrayObject $stationData */
        foreach ($results as $station => $stationData) {
            $columns = array_map('strtolower', array_keys($stationData->getArrayCopy()));
            $columns[] = 'station';

            $placeholders = $placeholders = $this->conn->createPlaceholders($columns);
            $query = 'INSERT INTO ' . self::TABLE. ' (' . implode($columns, ',') . ') VALUES (' . $placeholders . ')
                      ON DUPLICATE KEY UPDATE ' . $this->conn->onDuplicateSql($columns);
            $sth = $dbh->prepare($query);

            foreach ($stationData as $key => $value) {
                $value = $value instanceof \DateTime ? $value->format('Y-m-d H:i:s') : $value;
                $sth->bindValue(':' . strtolower($key), $value);
            }
            $sth->bindValue(':station', $station);

            try {
                $sth->execute();
            } catch (\PDOException $e) {
                $dbh->rollBack();
                throw $e;
            }
        }

        $dbh->commit();

        return true;
    }
}

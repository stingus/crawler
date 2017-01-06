<?php

namespace Stingus\Crawler\Weather;

use Stingus\Crawler\Crawler\Crawler;
use Stingus\Crawler\Exceptions\Weather\InvalidWeatherUnitException;

/**
 * Class WeatherCrawler
 *
 * @package Stingus\Crawler\Weather
 */
abstract class WeatherCrawler extends Crawler
{
    /** @var string */
    protected $unit;

    /** @var array */
    protected $stations;

    /** @var array */
    protected $stationsStatus;

    /**
     * WeatherCrawler constructor.
     *
     * @param string $sourceUrl Source URL
     * @param string $unit Units (C or F)
     * @param array  $stations Weather stations
     *
     * @throws InvalidWeatherUnitException
     */
    public function __construct($sourceUrl, $unit, array $stations)
    {
        if (!preg_match('/C|F/', $unit)) {
            throw new InvalidWeatherUnitException(sprintf('Weather unit %s is invalid', $unit));
        }
        $this->unit = $unit;
        $this->stations = $stations;
        $this->stationsStatus = [];
        parent::__construct($sourceUrl);
    }

    /**
     * Get the stations status
     *
     * @return array
     */
    public function getStationsStatus()
    {
        return $this->stationsStatus;
    }

    /**
     * Mark a station as failed
     *
     * @param $station
     */
    protected function markStationFailed($station)
    {
        $this->stationsStatus[$station] = false;
    }

    /**
     * Mark a station as successful
     *
     * @param $station
     */
    protected function markStationSuccessful($station)
    {
        $this->stationsStatus[$station] = true;
    }
}

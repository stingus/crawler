<?php

namespace Stingus\Crawler\Weather;

use Stingus\Crawler\Crawler\Crawler;

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
     * @param string $unit      Units (C or F)
     * @param array  $stations  Weather stations
     */
    public function __construct($sourceUrl, $unit, array $stations)
    {
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

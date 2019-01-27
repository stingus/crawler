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
    const UNIT_CELSIUS    = 'C';
    const UNIT_FAHRENHEIT = 'F';

    /** @var string */
    protected $unit;

    /** @var array */
    protected $stations;

    /** @var string */
    protected $apiKey;

    /** @var string */
    protected $lang;

    /** @var array */
    protected $stationsStatus;

    /**
     * WeatherCrawler constructor.
     *
     * @param string $sourceUrl Source URL
     * @param string $unit Units (C or F)
     * @param array  $stations Weather stations
     * @param string $apiKey ApiKey (optional, for certain services)
     * @param string $lang Weather language
     *
     * @throws InvalidWeatherUnitException
     * @throws \Stingus\Crawler\Exceptions\InvalidCrawlerUrlException
     */
    public function __construct($sourceUrl, $unit, array $stations, $apiKey = null, $lang = null)
    {
        $pregSearch = '/' . preg_quote(self::UNIT_CELSIUS, '/') .'|' . preg_quote(self::UNIT_FAHRENHEIT, '/') . '/';
        if (!preg_match($pregSearch, $unit)) {
            throw new InvalidWeatherUnitException(sprintf('Weather unit %s is invalid', $unit));
        }
        $this->unit = $unit;
        $this->stations = $stations;
        $this->apiKey = $apiKey;
        $this->lang = $lang;
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

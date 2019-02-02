<?php

namespace Stingus\Crawler\Weather;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Class OpenWeather.
 * Crawls for weather data using the openweathermap.org APIs
 *
 * @package Stingus\Crawler\Weather
 */
class OpenWeatherCrawler extends WeatherCrawler
{
    const MAX_DAYS_FORECAST = 5;
    const PATH_CURRENT  = '/weather';
    const PATH_FORECAST = '/forecast';

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function crawl()
    {
        $weatherCollection = new \ArrayObject();
        $stationDataProto = new \ArrayObject();

        foreach ($this->stations as $station) {
            $stationData = clone $stationDataProto;
            $domCrawlerCurrent = clone $this->domCrawler;
            $domCrawlerForecast = clone $this->domCrawler;

            $options = [
                'query' => [
                    'APPID' => $this->apiKey,
                    'id' => $station,
                    'units' => $this->getUnit(),
                    'mode' => 'xml',
                    'lang' => $this->lang,
                ],
            ];
            $domCrawlerCurrent->addContent($this->getContent(static::PATH_CURRENT, $options));
            $domCrawlerForecast->addContent($this->getContent(static::PATH_FORECAST, $options));

            $current = $domCrawlerCurrent->filterXPath('//current')->getNode(0);
            $forecasts = $domCrawlerForecast->filterXPath('//weatherdata/forecast');

            if (null === $current || null === $forecasts) {
                $this->markStationFailed($station);
                continue;
            }
            $this->markStationSuccessful($station);

            // Current
            $city = $current->getElementsByTagName('city')->item(0);
            $coord = $city->getElementsByTagName('coord')->item(0);
            $currentWeather = $current->getElementsByTagName('weather')->item(0);
            $wind = $current->getElementsByTagName('wind')->item(0);
            $sun = $city->getElementsByTagName('sun')->item(0);
            $stationData->offsetSet('city', $city->getAttribute('name'));
            $stationData->offsetSet('geo_lat', (float) $coord->getAttribute('lat'));
            $stationData->offsetSet('geo_long', (float) $coord->getAttribute('lon'));
            $stationData->offsetSet(
                'current_temp',
                (int)round($current->getElementsByTagName('temperature')->item(0)->getAttribute('value'))
            );
            $stationData->offsetSet(
                'current_code',
                (int) $currentWeather->getAttribute('number')
            );
            $stationData->offsetSet(
                'current_icon',
                $currentWeather->getAttribute('icon')
            );
            $stationData->offsetSet(
                'current_descr',
                $currentWeather->getAttribute('value')
            );
            $stationData->offsetSet(
                'current_wind_direction',
                (int) round($wind->getElementsByTagName('direction')->item(0)->getAttribute('value'))
            );
            $stationData->offsetSet(
                'current_wind_speed',
                (float) $wind->getElementsByTagName('speed')->item(0)->getAttribute('value')
            );
            $stationData->offsetSet(
                'current_atm_humidity',
                (int) $current->getElementsByTagName('humidity')->item(0)->getAttribute('value')
            );
            $stationData->offsetSet(
                'current_atm_pressure',
                (int) $current->getElementsByTagName('pressure')->item(0)->getAttribute('value')
            );
            $stationData->offsetSet(
                'current_atm_visibility',
                (float) $current->getElementsByTagName('visibility')->item(0)->getAttribute('value')
            );
            $stationData->offsetSet(
                'current_astro_sunrise',
                (new \DateTime($sun->getAttribute('rise')))->format('g:i a')
            );
            $stationData->offsetSet(
                'current_astro_sunset',
                (new \DateTime($sun->getAttribute('set')))->format('g:i a')
            );

            $highLow = $this->getTempHighLow($forecasts);

            $i = 0;
            foreach ($highLow as $forecast) {
                $stationData->offsetSet('forecast_date_' . $i, $forecast['date']);
                $stationData->offsetSet('forecast_code_' . $i, $forecast['code']);
                $stationData->offsetSet('forecast_icon_' . $i, $forecast['icon']);
                $stationData->offsetSet('forecast_descr_' . $i, $forecast['descr']);
                $stationData->offsetSet('forecast_high_' . $i, $forecast['high']);
                $stationData->offsetSet('forecast_low_' . $i, $forecast['low']);
                $i++;
            }

            $weatherCollection->offsetSet($station, $stationData);
        }

        return $weatherCollection;
    }

    /**
     * @param Crawler $forecasts
     *
     * @return array
     * @throws \Exception
     */
    private function getTempHighLow($forecasts)
    {
        $tempHighLow = [];
        $offset = 0;

        /** @var \DOMElement $forecast */
        foreach ($forecasts->children() as $forecast) {
            $dt = new \DateTime($forecast->getAttribute('from'));
            $dt0 = clone $dt;
            $dt0->setTime(0, 0);
            if (!isset($prevDt)) {
                $prevDt = clone $dt0;
            }
            if ($prevDt->getTimestamp() !== $dt0->getTimestamp()) {
                $offset++;
                $prevDt = clone $dt0;
            }
            if ($offset >= static::MAX_DAYS_FORECAST) {
                break;
            }

            $tempHighLowDay = &$tempHighLow[$offset];
            $tempHighLowDay['date'] = $dt0;

            $this->setForecastWeather($forecast, $dt, $tempHighLowDay);
        }

        return $tempHighLow;
    }

    /**
     * @param \DOMElement $forecast
     * @param \DateTime   $dt
     * @param array       $tempHighLowDay
     */
    private function setForecastWeather(\DOMElement $forecast, \DateTime $dt, array &$tempHighLowDay)
    {
        if (!array_key_exists('code', $tempHighLowDay) && 12 <= (int) $dt->format('H')) {
            $symbol = $forecast->getElementsByTagName('symbol')->item(0);
            $tempHighLowDay['code'] = (int) $symbol->getAttribute('number');
            $tempHighLowDay['icon'] = $symbol->getAttribute('var');
            $tempHighLowDay['descr'] = $symbol->getAttribute('name');
        }

        $temp = (int) round($forecast->getElementsByTagName('temperature')->item(0)->getAttribute('value'));

        if (!array_key_exists('high', $tempHighLowDay)
            || (array_key_exists('high', $tempHighLowDay) && $temp > $tempHighLowDay['high'])
        ) {
            $tempHighLowDay['high'] = $temp;
        }
        if (!array_key_exists('low', $tempHighLowDay)
            || (array_key_exists('low', $tempHighLowDay) && $temp < $tempHighLowDay['low'])
        ) {
            $tempHighLowDay['low'] = $temp;
        }
    }

    /**
     * @return string
     */
    private function getUnit()
    {
        $unit = 'metric';
        if ($this->unit === static::UNIT_FAHRENHEIT) {
            $unit = 'imperial';
        }

        return $unit;
    }
}

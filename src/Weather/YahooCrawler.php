<?php

namespace Stingus\Crawler\Weather;

/**
 * Class YahooCrawler.
 * Crawls for weather data using the Yahoo APIs
 *
 * @package Stingus\Crawler\Weather
 */
class YahooCrawler extends WeatherCrawler
{
    /**
     * @inheritDoc
     * @throws \InvalidArgumentException
     */
    public function crawl()
    {
        $weatherCollection = new \ArrayObject();
        $stationDataProto = new \ArrayObject();
        $this->domCrawler->setDefaultNamespacePrefix('yahoo');

        foreach ($this->stations as $station) {
            $stationData = clone $stationDataProto;
            $domCrawler = clone $this->domCrawler;

            /** @noinspection SqlResolve */
            $options = [
                'query' => [
                    'q' => 'select * from weather.forecast where woeid=' . $station . ' and u="' . $this->unit . '"'
                ]
            ];
            $domCrawler->addContent($this->getContent($options));

            if (0 === (int)$domCrawler->filterXPath('//query')->attr('yahoo:count')) {
                $this->markStationFailed($station);
                continue;
            }
            $this->markStationSuccessful($station);

            $channel = $domCrawler->filterXPath('//query/results/channel');
            $currentWind = $channel->filterXPath('//yweather:wind');
            $currentAtmosphere = $channel->filterXPath('//yweather:atmosphere');
            $currentAstronomy= $channel->filterXPath('//yweather:astronomy');
            $item = $channel->filterXPath('//item');
            $currentCondition = $item->filterXPath('//yweather:condition');
            $forecasts = $item->filterXPath('//yweather:forecast');

            $stationData->offsetSet(
                'build_date',
                new \DateTime($channel->filterXPath('//lastBuildDate')->getNode(0)->nodeValue)
            );
            $stationData->offsetSet('city', $channel->filterXPath('//yweather:location')->attr('city'));
            $stationData->offsetSet('geo_lat', (float)$item->filterXPath('//geo:lat')->getNode(0)->nodeValue);
            $stationData->offsetSet('geo_long', (float)$item->filterXPath('//geo:long')->getNode(0)->nodeValue);
            $stationData->offsetSet('current_temp', (int)$currentCondition->attr('temp'));
            $stationData->offsetSet('current_code', (int)$currentCondition->attr('code'));
            $stationData->offsetSet('current_wind_chill', (int)$currentWind->attr('chill'));
            $stationData->offsetSet('current_wind_direction', (int)$currentWind->attr('direction'));
            $stationData->offsetSet('current_wind_speed', (float)$currentWind->attr('speed'));
            $stationData->offsetSet('current_atm_humidity', (int)$currentAtmosphere->attr('humidity'));
            $stationData->offsetSet('current_atm_pressure', (float)$currentAtmosphere->attr('pressure'));
            $stationData->offsetSet('current_atm_visibility', (float)$currentAtmosphere->attr('visibility'));
            $stationData->offsetSet('current_astro_sunrise', $currentAstronomy->attr('sunrise'));
            $stationData->offsetSet('current_astro_sunset', $currentAstronomy->attr('sunset'));

            for ($i = 0, $iMax = $forecasts->count(); $i < $iMax; $i++) {
                $forecast = $forecasts->getNode($i);
                $stationData->offsetSet('forecast_date_' . $i, new \DateTime($forecast->getAttribute('date')));
                $stationData->offsetSet('forecast_code_' . $i, (int)$forecast->getAttribute('code'));
                $stationData->offsetSet('forecast_high_' . $i, (int)$forecast->getAttribute('high'));
                $stationData->offsetSet('forecast_low_' . $i, (int)$forecast->getAttribute('low'));
            }

            $weatherCollection->offsetSet($station, $stationData);
        }

        return $weatherCollection;
    }
}

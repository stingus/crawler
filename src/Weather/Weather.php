<?php

namespace Stingus\Crawler\Weather;

use Stingus\Crawler\Crawler\CrawlerAggregator;

/**
 * Class Weather
 *
 * @package Stingus\Crawler\Weather
 */
class Weather extends CrawlerAggregator
{
    /** @var array */
    private $crawlersStatus = [];

    /**
     * @inheritDoc
     * @throws \RuntimeException
     */
    public function crawl()
    {
        if (empty($this->crawlers)) {
            throw new \RuntimeException('There are no weather crawlers registered');
        }
        foreach ($this->crawlers as $crawler) {
            if (!$crawler instanceof WeatherCrawler) {
                throw new \RuntimeException(sprintf('Expected WeatherCrawler instance, got %s', get_class($crawler)));
            }
            $this->addResults($crawler->crawl());
            $this->addCrawlersStatus($crawler);
        }

        return $this->resultCollection;
    }

    /**
     * Get the crawler status
     *
     * @param bool $type Either true (successful) or false (failed) stations
     *
     * @return array
     */
    public function getCrawlerStatus($type)
    {
        $result = [];
        foreach ($this->crawlersStatus as $crawler => $crawlerStatus) {
            $stations = array_filter(
                $crawlerStatus,
                function ($status) use ($type) {
                    return $type === $status;
                }
            );
            if (count($stations) > 0) {
                $result[$crawler] = array_keys($stations);
            }
        }

        return $result;
    }

    /**
     * Add crawler status.
     * Get stations status from the crawler
     *
     * @param WeatherCrawler $crawler
     */
    private function addCrawlersStatus(WeatherCrawler $crawler)
    {
        foreach ($crawler->getStationsStatus() as $station => $status) {
            $this->crawlersStatus[get_class($crawler)][$station] = $status;
        }
    }
}

<?php

namespace Stingus\Crawler\Crawler;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

/**
 * Class CrawlerAggregator.
 * Base aggregator class for crawlers
 *
 * @package Stingus\Crawler\Crawler
 */
abstract class CrawlerAggregator
{
    /** @var DomCrawler */
    protected $domCrawler;

    /** @var Client */
    protected $client;

    /** @var array */
    protected $crawlers;

    /** @var \ArrayObject */
    protected $resultCollection;

    /**
     * CrawlerAggregator constructor.
     *
     * @param DomCrawler $domCrawler DOM Crawler
     * @param Client     $client     HTTP client
     */
    public function __construct(DomCrawler $domCrawler, Client $client)
    {
        $this->domCrawler = $domCrawler;
        $this->client = $client;
        $this->crawlers = [];
        $this->resultCollection = new \ArrayObject();
    }

    /**
     * Register a new crawler
     *
     * @param Crawler $crawler
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function registerCrawler(Crawler $crawler)
    {
        $hash = spl_object_hash($crawler);
        if (array_key_exists($hash, $this->crawlers)) {
            throw new \InvalidArgumentException(sprintf('Crawler already registered (%s)', get_class($crawler)));
        }
        $crawler->setDomCrawler($this->domCrawler);
        $crawler->setClient($this->client);
        $this->crawlers[$hash] = $crawler;

        return $this;
    }

    /**
     * Add results from one crawler to the collection
     *
     * @param \ArrayObject $results
     *
     * @throws \RuntimeException
     */
    protected function addResults(\ArrayObject $results)
    {
        foreach ($results as $key => $value) {
            if ($this->resultCollection->offsetExists($key)) {
                throw new \RuntimeException(sprintf('Key "%s" already exists', $key));
            }
            $this->resultCollection->offsetSet($key, $value);
        }
    }

    /**
     * Get data from all registered crawlers
     *
     * @return \ArrayObject
     */
    abstract public function crawl();
}

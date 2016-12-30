<?php

namespace Stingus\Crawler\Exchange;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

/**
 * Class Exchange
 *
 * @package Stingus\Crawler\Exchange
 */
class Exchange
{
    /** @var DomCrawler */
    private $domCrawler;

    /** @var Client */
    private $client;

    /** @var array */
    private $crawlers;

    /** @var \ArrayObject */
    private $rateCollection;

    /** @var \DateTime */
    private $date;

    /**
     * ExchangeCrawler constructor.
     *
     * @param DomCrawler $domCrawler DOM Crawler
     * @param Client     $client     HTTP client
     */
    public function __construct(DomCrawler $domCrawler, Client $client)
    {
        $this->domCrawler = $domCrawler;
        $this->client = $client;
        $this->crawlers = [];
        $this->rateCollection = new \ArrayObject();
    }

    /**
     * Get data from all registered crawlers
     *
     * @return \ArrayObject
     * @throws \RuntimeException
     */
    public function crawl()
    {
        if (empty($this->crawlers)) {
            throw new \RuntimeException('There are no exchange crawlers registered');
        }
        /** @var ExchangeCrawler $crawler */
        foreach ($this->crawlers as $crawler) {
            $this->addResults($crawler->crawl());
            if (null === $this->date) {
                $this->date = $crawler->getDate();
            }
        }

        return $this->rateCollection;
    }

    /**
     * Register a new crawler
     *
     * @param ExchangeCrawler $crawler
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function registerCrawler(ExchangeCrawler $crawler)
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
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Add results from one crawler to the collection
     *
     * @param \ArrayObject $results
     *
     * @throws \RuntimeException
     */
    private function addResults(\ArrayObject $results)
    {
        foreach ($results as $abbr => $rate) {
            if ($this->rateCollection->offsetExists($abbr)) {
                throw new \RuntimeException(sprintf('Rate "%s" already exists', $abbr));
            }
            $this->rateCollection->offsetSet($abbr, $rate);
        }
    }
}

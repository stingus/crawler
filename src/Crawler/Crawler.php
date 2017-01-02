<?php

namespace Stingus\Crawler\Crawler;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

/**
 * Class Crawler.
 * Base Crawler
 *
 * @package Stingus\Crawler\Exchange
 */
abstract class Crawler
{
    /** @var DomCrawler */
    protected $domCrawler;

    /** @var Client */
    protected $client;

    /** @var string */
    protected $sourceUrl;

    /**
     * Crawler constructor.
     *
     * @param $sourceUrl
     */
    public function __construct($sourceUrl)
    {
        $this->sourceUrl = $sourceUrl;
    }

    /**
     * @param DomCrawler $domCrawler
     *
     * @return Crawler
     */
    public function setDomCrawler(DomCrawler $domCrawler)
    {
        $this->domCrawler = $domCrawler;

        return $this;
    }

    /**
     * @param Client $client
     *
     * @return Crawler
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @param array $options Request options
     *
     * @return string
     */
    protected function getContent(array $options = array())
    {
        return $this->client->get($this->sourceUrl, $options)->getBody();
    }

    /**
     * @return \ArrayObject
     */
    abstract public function crawl();
}

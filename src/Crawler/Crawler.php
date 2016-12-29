<?php

namespace Stingus\Crawler\Crawler;

use Guzzle\Http\Client;
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

    /** @var string */
    private $content;

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
     * @return string
     * @throws \Guzzle\Http\Exception\RequestException
     */
    protected function getContent()
    {
        if (null === $this->content) {
            $this->content = $this->client->get($this->sourceUrl)->send()->getBody(true);
        }

        return $this->content;
    }

    /**
     * @return \ArrayObject
     */
    abstract public function crawl();
}

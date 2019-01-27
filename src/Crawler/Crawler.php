<?php

namespace Stingus\Crawler\Crawler;

use GuzzleHttp\Client;
use Psr\Http\Message\StreamInterface;
use Stingus\Crawler\Exceptions\InvalidCrawlerUrlException;
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
     *
     * @throws InvalidCrawlerUrlException
     */
    public function __construct($sourceUrl)
    {
        if (!filter_var($sourceUrl, FILTER_VALIDATE_URL)
            || !preg_match('/http|https/', parse_url($sourceUrl, PHP_URL_SCHEME))
        ) {
            throw new InvalidCrawlerUrlException(sprintf('Invalid source URL %s', $sourceUrl));
        }
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
     * @param string $path    Path to append to url
     * @param array  $options Request options
     *
     * @return StreamInterface
     */
    protected function getContent($path = '', array $options = null)
    {
        return $this->client->get($this->sourceUrl . $path, $options)->getBody();
    }

    /**
     * @return \ArrayObject
     */
    abstract public function crawl();
}

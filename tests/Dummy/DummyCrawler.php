<?php

namespace Stingus\Crawler\Test\Dummy;

use Stingus\Crawler\Crawler\Crawler;

/**
 * Class DummyCrawler
 *
 * @package Stingus\Crawler\Test\Dummy
 */
class DummyCrawler extends Crawler
{
    /** @var string */
    private $path;

    /** @var array */
    private $options;

    /**
     * DummyCrawler constructor.
     *
     * @param string $url
     * @param string $path
     * @param array $options
     *
     * @throws \Stingus\Crawler\Exceptions\InvalidCrawlerUrlException
     */
    public function __construct($url, $path = null, array $options = null)
    {
        parent::__construct($url);
        $this->path = $path;
        $this->options = $options;
    }

    /**
     * @inheritDoc
     */
    public function crawl()
    {
        $this->getContent($this->path, $this->options);
        return new \ArrayObject();
    }
}

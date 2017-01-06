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
    /**
     * @inheritDoc
     */
    public function crawl()
    {
        $this->getContent();
        return new \ArrayObject();
    }
}

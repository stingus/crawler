<?php

namespace Stingus\Crawler\Exchange;

use Stingus\Crawler\Crawler\CrawlerAggregator;

/**
 * Class Exchange
 *
 * @package Stingus\Crawler\Exchange
 */
class Exchange extends CrawlerAggregator
{
    /** @var \DateTime */
    private $date;

    /**
     * @inheritdoc
     * @throws \RuntimeException
     */
    public function crawl()
    {
        if (empty($this->crawlers)) {
            throw new \RuntimeException('There are no exchange crawlers registered');
        }
        foreach ($this->crawlers as $crawler) {
            if (!$crawler instanceof ExchangeCrawler) {
                throw new \RuntimeException(sprintf('Expected ExchangeCrawler instance, got %s', get_class($crawler)));
            }
            $this->addResults($crawler->crawl());
            if (null === $this->date) {
                $this->date = $crawler->getDate();
            }
        }

        return $this->resultCollection;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}

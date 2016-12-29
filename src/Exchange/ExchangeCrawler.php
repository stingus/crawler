<?php

namespace Stingus\Crawler\Exchange;

use Stingus\Crawler\Crawler\Crawler;

/**
 * Class ExchangeCrawler
 *
 * @package Stingus\Crawler\Exchange
 */
abstract class ExchangeCrawler extends Crawler
{
    /** @var \DateTime */
    protected $date;

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return ExchangeCrawler
     */
    abstract protected function setDate();
}

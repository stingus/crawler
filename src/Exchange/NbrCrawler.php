<?php

namespace Stingus\Crawler\Exchange;

/**
 * Class NbrCrawler.
 * Crawler for National Bank of Romania
 *
 * @package Stingus\Crawler\Exchange
 */
class NbrCrawler extends ExchangeCrawler
{
    /**
     * @inheritdoc
     * @throws \Guzzle\Http\Exception\RequestException
     * @throws \RuntimeException
     */
    public function crawl()
    {
        $this->domCrawler->addContent($this->getContent());
        $this->setDate();

        $rateElements = $this->domCrawler->filterXPath('//default:DataSet/default:Body/default:Cube/*');

        $rateCollection = new \ArrayObject();

        /** @var \DOMElement $rateElement */
        foreach ($rateElements as $rateElement) {
            if (('' !== $value = $rateElement->nodeValue)
                && ('' !== $abbr = $rateElement->getAttribute('currency'))
            ) {
                $rateCollection->offsetSet($abbr, (float)$value);
            }
        }

        return $rateCollection;
    }

    /**
     * @inheritdoc
     */
    protected function setDate()
    {
        $date = $this
            ->domCrawler
            ->filterXPath('//default:DataSet/default:Body/default:Cube/@date')
            ->getNode(0)
            ->nodeValue;

        $this->date = new \DateTime($date);

        return $this;
    }
}

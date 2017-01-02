<?php

namespace Stingus\Crawler\Exchange;

use Stingus\Crawler\Exceptions\Exchange\InvalidExchangeDateException;
use Stingus\Crawler\Exceptions\Exchange\InvalidExchangeRateValueException;

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
     * @throws \RuntimeException
     * @throws InvalidExchangeDateException
     * @throws InvalidExchangeRateValueException
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
                && ('' !== $key = $rateElement->getAttribute('currency'))
            ) {
                if (!preg_match('/\d+/', $rateElement->nodeValue)) {
                    throw new InvalidExchangeRateValueException(
                        sprintf('Invalid value for currency %s and crawler %s', $key, get_class($this))
                    );
                }
                $rateCollection->offsetSet($key, (float)$value);
            }
        }

        return $rateCollection;
    }

    /**
     * @inheritdoc
     * @throws InvalidExchangeDateException
     */
    protected function setDate()
    {
        $dateCrawler = $this
            ->domCrawler
            ->filterXPath('//default:DataSet/default:Body/default:Cube/@date');

        if (null === $dateNode = $dateCrawler->getNode(0)) {
            throw new InvalidExchangeDateException('Exchange reference date is empty');
        }

        $this->date = new \DateTime($dateNode->nodeValue);

        return $this;
    }
}

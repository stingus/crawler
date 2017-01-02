<?php

namespace Stingus\Crawler\Exchange;

use Stingus\Crawler\Exceptions\Exchange\InvalidExchangeRateValueException;

/**
 * Class InforeuroCrawler.
 * Crawler for Inforeuro exchange rate
 *
 * @package Stingus\Crawler\Exchange
 */
class InforeuroCrawler extends ExchangeCrawler
{
    const INFOREURO_ABBR = 'inforeuro';

    /**
     * @inheritDoc
     * @throws \RuntimeException
     * @throws InvalidExchangeRateValueException
     */
    public function crawl()
    {
        /** @var array $rateElements */
        if (null === $rateElements = json_decode($this->getContent(), true)) {
            throw new \RuntimeException('Error trying to fetch the Inforeuro source');
        }
        $this->setDate();

        $rateCollection = new \ArrayObject();

        $rateFound = false;
        foreach ($rateElements as $rateElement) {
            if (array_key_exists('isoA2Code', $rateElement)
                && array_key_exists('value', $rateElement)
                && $rateElement['isoA2Code'] === 'RO'
            ) {
                if (!preg_match('/\d+/', $rateElement['value'])) {
                    throw new InvalidExchangeRateValueException(
                        sprintf('Invalid value for currency %s and crawler %s', 'Inforeuro', get_class($this))
                    );
                }
                $rateCollection->offsetSet(self::INFOREURO_ABBR, (float)$rateElement['value']);
                $rateFound = true;
                break;
            }
        }

        if (!$rateFound) {
            throw new \RuntimeException('Inforeuro not found');
        }

        return $rateCollection;
    }

    /**
     * @inheritDoc
     */
    protected function setDate()
    {
        return $this;
    }
}

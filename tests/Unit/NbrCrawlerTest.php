<?php

namespace Stingus\Crawler\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stingus\Crawler\Exchange\NbrCrawler;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

/**
 * Class NbrCrawlerTest
 *
 * @group Unit
 */
class NbrCrawlerTest extends TestCase
{
    use MockClientTrait;

    public function testNbrCrawlerValidData()
    {
        $nbrCrawler = new NbrCrawler('');
        $client = $this->getMockClient(200, 'exchange/nbr_valid.xml');
        $nbrCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $result = $nbrCrawler->crawl();
        $this->assertEquals($result, $this->expectedValid());
    }

    public function testNbrCrawlerHasDate()
    {
        $nbrCrawler = new NbrCrawler('');
        $client = $this->getMockClient(200, 'exchange/nbr_valid.xml');
        $nbrCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $nbrCrawler->crawl();
        $this->assertEquals(new \DateTime('2016-12-30'), $nbrCrawler->getDate());
    }

    public function testNbrCrawlerInvalidData()
    {
        $nbrCrawler = new NbrCrawler('');
        $client = $this->getMockClient(200, 'exchange/nbr_valid_empty.xml');
        $nbrCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $result = $nbrCrawler->crawl();
        $this->assertEmpty($result);
    }

    /**
     * @expectedException \Stingus\Crawler\Exceptions\Exchange\InvalidExchangeRateValueException
     * @expectedExceptionMessageRegExp /Invalid value for currency USD and crawler [a-zA-Z0-9_]+/
     */
    public function testNbrCrawlerInvalidValue()
    {
        $nbrCrawler = new NbrCrawler('');
        $client = $this->getMockClient(200, 'exchange/nbr_invalid_value.xml');
        $nbrCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $nbrCrawler->crawl();
    }

    /**
     * @expectedException \Stingus\Crawler\Exceptions\Exchange\InvalidExchangeDateException
     * @expectedExceptionMessage Exchange reference date is empty
     */
    public function testNbrCrawlerInvalidDate()
    {
        $nbrCrawler = new NbrCrawler('');
        $client = $this->getMockClient(200, 'exchange/nbr_invalid_date.xml');
        $nbrCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $nbrCrawler->crawl();
    }

    /**
     * @dataProvider errorStatusCodeProvider
     * @expectedException \GuzzleHttp\Exception\RequestException
     *
     * @param $responseCode
     */
    public function testNbrCrawlerStatusCodeError($responseCode)
    {
        $client = $this->getMockClient($responseCode);
        $nbrCrawler = new NbrCrawler('');
        $nbrCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $nbrCrawler->crawl();
    }

    /**
     * @return \ArrayObject
     */
    private function expectedValid()
    {
        return new \ArrayObject([
            'AED' => 0.0001,
            'AUD' => 1.0010,
            'BGN' => 2.0100,
            'BRL' => 3.1000,
            'CAD' => 4.9000,
            'CHF' => 5.0900,
            'CNY' => 6.0090,
            'CZK' => 7.0009,
            'DKK' => 8.0099,
            'EGP' => 9.0999,
            'EUR' => 10.9999,
            'GBP' => 11.0000,
            'HRK' => 12.1111,
            'HUF' => 13.2222,
            'INR' => 14.3333,
            'JPY' => 15.4444,
            'KRW' => 16.5555,
            'MDL' => 17.6666,
            'MXN' => 18.7777,
            'NOK' => 19.8888,
            'NZD' => 20.9999,
            'PLN' => 21.00001,
            'RSD' => 22.0000,
            'RUB' => 23.00004,
            'SEK' => 24.00005,
            'TRY' => 25.00006,
            'UAH' => 26.0000,
            'USD' => 27.0000,
            'XAU' => 28.0000,
            'XDR' => 29.0000,
            'ZAR' => 30.0000,
        ]);
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    public function errorStatusCodeProvider()
    {
        return [
            [400],
            [401],
            [403],
            [404],
            [405],
            [406],
            [408],
            [409],
            [410],
            [429],
            [500],
            [501],
            [502],
            [503],
            [504],
        ];
    }
}

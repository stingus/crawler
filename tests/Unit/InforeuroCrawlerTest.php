<?php

namespace Stingus\Crawler\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stingus\Crawler\Exchange\InforeuroCrawler;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

/**
 * Class InforeuroCrawlerTest
 * @group Unit
 */
class InforeuroCrawlerTest extends TestCase
{
    use MockClientTrait;

    public function testInforeuroCrawlerValidData()
    {
        $inforeuroCrawler = new InforeuroCrawler('http://example.com');
        $client = $this->getMockClient(200, 'exchange/inforeuro_valid.json');
        $inforeuroCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $result = $inforeuroCrawler->crawl();
        $this->assertEquals($result, $this->expectedValid());
    }

    public function testInforeuroCrawlerEmptyDate()
    {
        $inforeuroCrawler = new InforeuroCrawler('http://example.com');
        $client = $this->getMockClient(200, 'exchange/inforeuro_valid.json');
        $inforeuroCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $inforeuroCrawler->crawl();
        $this->assertNull($inforeuroCrawler->getDate());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Error trying to fetch the Inforeuro source
     */
    public function testInforeuroCrawlerInvalidData()
    {
        $inforeuroCrawler = new InforeuroCrawler('http://example.com');
        $client = $this->getMockClient(200, 'exchange/inforeuro_invalid.json');
        $inforeuroCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $inforeuroCrawler->crawl();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Inforeuro not found
     */
    public function testInforeuroCrawlerNoCountry()
    {
        $inforeuroCrawler = new InforeuroCrawler('http://example.com');
        $client = $this->getMockClient(200, 'exchange/inforeuro_invalid_no_country.json');
        $inforeuroCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $inforeuroCrawler->crawl();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Inforeuro not found
     */
    public function testInforeuroCrawlerMissingCountry()
    {
        $inforeuroCrawler = new InforeuroCrawler('http://example.com');
        $client = $this->getMockClient(200, 'exchange/inforeuro_invalid_missing_country.json');
        $inforeuroCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $inforeuroCrawler->crawl();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Inforeuro not found
     */
    public function testInforeuroCrawlerNoValue()
    {
        $inforeuroCrawler = new InforeuroCrawler('http://example.com');
        $client = $this->getMockClient(200, 'exchange/inforeuro_invalid_no_value.json');
        $inforeuroCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $inforeuroCrawler->crawl();
    }

    /**
     * @expectedException \Stingus\Crawler\Exceptions\Exchange\InvalidExchangeRateValueException
     * @expectedExceptionMessageRegExp /Invalid value for currency Inforeuro and crawler [a-zA-Z0-9_]+/
     */
    public function testInforeuroCrawlerInvalidValue()
    {
        $inforeuroCrawler = new InforeuroCrawler('http://example.com');
        $client = $this->getMockClient(200, 'exchange/inforeuro_invalid_value.json');
        $inforeuroCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $inforeuroCrawler->crawl();
    }

    /**
     * @return \ArrayObject
     */
    private function expectedValid()
    {
        return new \ArrayObject([
            'inforeuro' => 4.1234,
        ]);
    }
}

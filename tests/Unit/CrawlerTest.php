<?php

namespace Stingus\Crawler\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stingus\Crawler\Test\Dummy\DummyCrawler;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

/**
 * Class CrawlerTest
 * @group Unit
 */
class CrawlerTest extends TestCase
{
    use MockClientTrait;

    /**
     * @dataProvider validUrlProvider
     * @param $url
     */
    public function testValidUrl($url)
    {
        new DummyCrawler($url);
    }

    /**
     * @dataProvider invalidUrlProvider
     * @expectedException \Stingus\Crawler\Exceptions\InvalidCrawlerUrlException
     * @param $url
     */
    public function testInvalidUrl($url)
    {
        new DummyCrawler($url);
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
        $crawler = new DummyCrawler('http://example.com');
        $crawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $crawler->crawl();
    }

    /**
     * @return array
     */
    public function validUrlProvider()
    {
        return [
            ['http://example.com'],
            ['http://www.example.com'],
            ['http://www.example.com/path'],
            ['http://www.example.com/path?a=1&b=2'],
            ['http://www.example.com/path?a[]=1&a[]=2'],
            ['https://example.com'],
            ['https://www.example.com'],
            ['https://www.example.com/path'],
            ['https://www.example.com/path?a=1&b=2'],
            ['https://www.example.com/path?a[]=1&a[]=2'],
        ];
    }

    /**
     * @return array
     */
    public function invalidUrlProvider()
    {
        return [
            [''],
            [1],
            [true],
            [false],
            ['ftp://example.com'],
            ['ssh://example.com'],
            ['mailto://example.com'],
            ['example.com/path'],
            ['example'],
            ['http://'],
        ];
    }

    /**
     * @return array
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

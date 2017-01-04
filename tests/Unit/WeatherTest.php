<?php

namespace Stingus\Crawler\Test\Unit;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Stingus\Crawler\Crawler\Crawler;
use Stingus\Crawler\Weather\Weather;
use Stingus\Crawler\Weather\WeatherCrawler;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

/**
 * Class WeatherTest
 * @group Unit
 */
class WeatherTest extends TestCase
{
    public function testWeatherCrawlWithRegisteredCrawlers()
    {
        $weather = new Weather(new DomCrawler(), new Client());
        $crawlerMock1 = $this->getCrawlerMock();
        $crawlerMock2 = $this->getCrawlerMock();
        $crawlerMock1
            ->expects($this->once())
            ->method('crawl')
            ->willReturn(new \ArrayObject());
        $crawlerMock2
            ->expects($this->once())
            ->method('crawl')
            ->willReturn(new \ArrayObject());
        /** @noinspection PhpParamsInspection */
        $actual = $weather
            ->registerCrawler($crawlerMock1)
            ->registerCrawler($crawlerMock2)
            ->crawl();

        $this->assertInstanceOf(\ArrayObject::class, $actual);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage There are no weather crawlers registered
     */
    public function testWeatherCrawlWithoutRegisteredCrawlers()
    {
        $weather = new Weather(new DomCrawler(), new Client());
        $weather->crawl();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /Expected WeatherCrawler instance, got [a-zA-Z0-9_]+/
     */
    public function testWeatherCrawlWithInvalidCrawler()
    {
        $weather = new Weather(new DomCrawler(), new Client());
        $crawler = $this
            ->getMockBuilder(Crawler::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @noinspection PhpParamsInspection */
        $weather->registerCrawler($crawler);
        $weather->crawl();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /Crawler already registered \([a-zA-Z0-9_]+\)/
     */
    public function testWeatherRegisterSameCrawlerTwice()
    {
        $weather = new Weather(new DomCrawler(), new Client());
        $crawlerMock = $this->getCrawlerMock();
        /** @noinspection PhpParamsInspection */
        $weather
            ->registerCrawler($crawlerMock)
            ->registerCrawler($crawlerMock);
    }

    public function testWeatherCrawlSingleCrawlerHasData()
    {
        $weather = new Weather(new DomCrawler(), new Client());
        $results = new \ArrayObject([
            0 => new \ArrayObject(['a' => 1, 'b' => 2, 'c' => 3]),
            1 => new \ArrayObject(['d' => 4, 'e' => 5, 'f' => 6])
        ]);
        $crawlerMock = $this->getCrawlerMock();
        $crawlerMock
            ->expects($this->once())
            ->method('crawl')
            ->willReturn($results);

        /** @noinspection PhpParamsInspection */
        $actual = $weather->registerCrawler($crawlerMock)->crawl();

        $this->assertEquals($results, $actual);
    }

    public function testWeatherCrawlMultipleCrawlersHasData()
    {
        $weather = new Weather(new DomCrawler(), new Client());

        $results1 = new \ArrayObject([
            0 => new \ArrayObject(['a' => 1, 'b' => 2, 'c' => 3]),
            1 => new \ArrayObject(['d' => 4, 'e' => 5, 'f' => 6])
        ]);
        $results2 = new \ArrayObject([
            2 => new \ArrayObject(['a' => 1, 'b' => 2, 'c' => 3]),
            3 => new \ArrayObject(['d' => 4, 'e' => 5, 'f' => 6])
        ]);
        $crawlerMock1 = $this->getCrawlerMock();
        $crawlerMock1
            ->expects($this->once())
            ->method('crawl')
            ->willReturn($results1);
        $crawlerMock2 = $this->getCrawlerMock();
        $crawlerMock2
            ->expects($this->once())
            ->method('crawl')
            ->willReturn($results2);
        /** @noinspection PhpParamsInspection */
        $weather
            ->registerCrawler($crawlerMock1)
            ->registerCrawler($crawlerMock2);

        $expected = new \ArrayObject(array_merge($results1->getArrayCopy(), $results2->getArrayCopy()));

        $this->assertEquals($expected, $weather->crawl());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /Key "0" already exists/
     */
    public function testWeatherCrawlMultipleCrawlersWithDuplicateKeysInData()
    {
        $weather = new Weather(new DomCrawler(), new Client());
        $results1 = new \ArrayObject([
            0 => new \ArrayObject(['a' => 1, 'b' => 2, 'c' => 3]),
            1 => new \ArrayObject(['d' => 4, 'e' => 5, 'f' => 6])
        ]);
        $results2 = new \ArrayObject([
            0 => new \ArrayObject(['a' => 1, 'b' => 2, 'c' => 3]),
        ]);
        $crawlerMock1 = $this->getCrawlerMock();
        $crawlerMock1
            ->expects($this->once())
            ->method('crawl')
            ->willReturn($results1);
        $crawlerMock2 = $this->getCrawlerMock();
        $crawlerMock2
            ->expects($this->once())
            ->method('crawl')
            ->willReturn($results2);

        /** @noinspection PhpParamsInspection */
        $weather
            ->registerCrawler($crawlerMock1)
            ->registerCrawler($crawlerMock2)
            ->crawl();
    }

    public function testWeatherCrawlStatus()
    {
        $weather = new Weather(new DomCrawler(), new Client());

        $crawlerMock1 = $this
            ->getMockBuilder(WeatherCrawler::class)
            ->setMockClassName('WeatherCrawler1')
            ->setConstructorArgs(['', 'C', []])
            ->setMethods(['crawl', 'getStationsStatus'])
            ->getMock();
        $crawlerMock1
            ->expects($this->once())
            ->method('getStationsStatus')
            ->willReturn([0 => true, 1 => false]);
        $crawlerMock1
            ->expects($this->once())
            ->method('crawl')
            ->willReturn(new \ArrayObject());

        $crawlerMock2 = $this
            ->getMockBuilder(WeatherCrawler::class)
            ->setMockClassName('WeatherCrawler2')
            ->setConstructorArgs(['', 'C', []])
            ->setMethods(['crawl', 'getStationsStatus'])
            ->getMock();
        $crawlerMock2
            ->expects($this->once())
            ->method('getStationsStatus')
            ->willReturn([0 => true, 1 => false]);
        $crawlerMock2
            ->expects($this->once())
            ->method('crawl')
            ->willReturn(new \ArrayObject());

        /** @noinspection PhpParamsInspection */
        $weather
            ->registerCrawler($crawlerMock1)
            ->registerCrawler($crawlerMock2);
        $weather->crawl();

        $this->assertEquals(
            [get_class($crawlerMock1) => [0], get_class($crawlerMock2) => [0]],
            $weather->getCrawlerStatus(true)
        );
        $this->assertEquals(
            [get_class($crawlerMock1) => [1], get_class($crawlerMock2) => [1]],
            $weather->getCrawlerStatus(false)
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getCrawlerMock()
    {
        return $this
            ->getMockBuilder(WeatherCrawler::class)
            ->setConstructorArgs(['', 'C', []])
            ->setMethods(['crawl'])
            ->getMock();
    }
}

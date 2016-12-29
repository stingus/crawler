<?php

namespace Stingus\Crawler\Test\Unit;

use Guzzle\Http\Client;
use PHPUnit\Framework\TestCase;
use Stingus\Crawler\Exchange\Exchange;
use Stingus\Crawler\Exchange\ExchangeCrawler;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

/**
 * Class ExchangeTest
 * @group Unit
 */
class ExchangeTest extends TestCase
{
    public function testExchangeCrawlWithRegisteredCrawlers()
    {
        $exchange = new Exchange(new DomCrawler(), new Client());
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
        $actual = $exchange
            ->registerCrawler($crawlerMock1)
            ->registerCrawler($crawlerMock2)
            ->crawl();

        $this->assertInstanceOf(\ArrayObject::class, $actual);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage There are no exchange crawlers registered
     */
    public function testExchangeCrawlWithoutRegisteredCrawlers()
    {
        $exchange = new Exchange(new DomCrawler(), new Client());
        $exchange->crawl();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /Crawler already registered \([a-zA-Z0-9_]+\)/
     */
    public function testExchangeRegisterSameCrawlerTwice()
    {
        $exchange = new Exchange(new DomCrawler(), new Client());
        $crawlerMock = $this->getCrawlerMock();
        /** @noinspection PhpParamsInspection */
        $exchange
            ->registerCrawler($crawlerMock)
            ->registerCrawler($crawlerMock);
    }

    public function testExchangeCrawlHasDate()
    {
        $exchange = new Exchange(new DomCrawler(), new Client());
        $date = new \DateTime('2016-12-31');
        $crawlerMock = $this->getCrawlerMock();
        $crawlerMock
            ->expects($this->once())
            ->method('crawl')
            ->willReturn(new \ArrayObject());
        $crawlerMock
            ->expects($this->once())
            ->method('getDate')
            ->willReturn($date);

        /** @noinspection PhpParamsInspection */
        $exchange->registerCrawler($crawlerMock)->crawl();
        $actual = $exchange->getDate();

        $this->assertSame($date, $actual);
    }

    public function testExchangeCrawlHasFirstCrawlerDate()
    {
        $exchange = new Exchange(new DomCrawler(), new Client());
        $date = new \DateTime('2016-12-31');
        $crawlerMock1 = $this->getCrawlerMock();
        $crawlerMock2 = $this->getCrawlerMock();
        $crawlerMock1
            ->expects($this->once())
            ->method('crawl')
            ->willReturn(new \ArrayObject());
        $crawlerMock1
            ->expects($this->once())
            ->method('getDate')
            ->willReturn($date);
        $crawlerMock2
            ->expects($this->once())
            ->method('crawl')
            ->willReturn(new \ArrayObject());
        $crawlerMock2
            ->expects($this->never())
            ->method('getDate');

        /** @noinspection PhpParamsInspection */
        $exchange
            ->registerCrawler($crawlerMock1)
            ->registerCrawler($crawlerMock2)
            ->crawl();
        $actual = $exchange->getDate();

        $this->assertSame($date, $actual);
    }

    public function testExchangeCrawlHasSecondCrawlerDate()
    {
        $exchange = new Exchange(new DomCrawler(), new Client());
        $date = new \DateTime('2016-12-31');
        $crawlerMock1 = $this->getCrawlerMock();
        $crawlerMock2 = $this->getCrawlerMock();
        $crawlerMock1
            ->expects($this->once())
            ->method('crawl')
            ->willReturn(new \ArrayObject());
        $crawlerMock1
            ->expects($this->once())
            ->method('getDate')
            ->willReturn(null);
        $crawlerMock2
            ->expects($this->once())
            ->method('crawl')
            ->willReturn(new \ArrayObject());
        $crawlerMock2
            ->expects($this->once())
            ->method('getDate')
            ->willReturn($date);

        /** @noinspection PhpParamsInspection */
        $exchange
            ->registerCrawler($crawlerMock1)
            ->registerCrawler($crawlerMock2)
            ->crawl();
        $actual = $exchange->getDate();

        $this->assertSame($date, $actual);
    }

    public function testExchangeCrawlSingleCrawlerHasData()
    {
        $exchange = new Exchange(new DomCrawler(), new Client());
        $results = new \ArrayObject([
            'a' => 1,
            'b' => 2,
            'c' => 3
        ]);
        $crawlerMock = $this->getCrawlerMock();
        $crawlerMock
            ->expects($this->once())
            ->method('crawl')
            ->willReturn($results);

        /** @noinspection PhpParamsInspection */
        $actual = $exchange->registerCrawler($crawlerMock)->crawl();

        $this->assertEquals($results, $actual);
    }

    public function testExchangeCrawlMultipleCrawlersHasData()
    {
        $exchange = new Exchange(new DomCrawler(), new Client());
        $results1 = new \ArrayObject([
            'a' => 1,
            'b' => 2,
            'c' => 3
        ]);
        $results2 = new \ArrayObject([
            'd' => 4,
            'e' => 5,
            'f' => 6
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
        $actual = $exchange
            ->registerCrawler($crawlerMock1)
            ->registerCrawler($crawlerMock2)
            ->crawl();

        $expected = new \ArrayObject(array_merge($results1->getArrayCopy(), $results2->getArrayCopy()));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /Rate "b" already exists/
     */
    public function testExchangeCrawlMultipleCrawlersWithDuplicateKeysInData()
    {
        $exchange = new Exchange(new DomCrawler(), new Client());
        $results1 = new \ArrayObject([
            'a' => 1,
            'b' => 2,
            'c' => 3
        ]);
        $results2 = new \ArrayObject([
            'd' => 4,
            'b' => 5,
            'f' => 6
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
        $exchange
            ->registerCrawler($crawlerMock1)
            ->registerCrawler($crawlerMock2)
            ->crawl();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getCrawlerMock()
    {
        return $this
            ->getMockBuilder(ExchangeCrawler::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}

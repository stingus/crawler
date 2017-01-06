<?php

namespace Stingus\Crawler\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stingus\Crawler\Weather\YahooCrawler;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

/**
 * Class YahooCrawlerTest
 * @group Unit
 */
class YahooCrawlerTest extends TestCase
{
    use MockClientTrait;

    public function testCelsiusUnit()
    {
        $yahooCrawler = new YahooCrawler('http://example.com', 'C', []);
        $this->assertInstanceOf(YahooCrawler::class, $yahooCrawler);
    }

    public function testFahrenheitUnit()
    {
        $yahooCrawler = new YahooCrawler('http://example.com', 'F', []);
        $this->assertInstanceOf(YahooCrawler::class, $yahooCrawler);
    }

    /**
     * @dataProvider invalidUnitProvider
     * @expectedException \Stingus\Crawler\Exceptions\Weather\InvalidWeatherUnitException
     *
     * @param mixed $unit
     */
    public function testInvalidUnit($unit)
    {
        new YahooCrawler('http://example.com', $unit, []);
    }

    public function testYahooCrawlValid()
    {
        $yahooCrawler = new YahooCrawler('http://example.com', 'C', [0]);
        $client = $this->getMockClient(200, 'weather/yahoo_valid.xml');
        $yahooCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $result = $yahooCrawler->crawl();
        $this->assertEquals($this->expectedValid(), $result);
        $this->assertTrue($yahooCrawler->getStationsStatus()[0]);
    }

    public function testYahooCrawlEmpty()
    {
        $yahooCrawler = new YahooCrawler('http://example.com', 'C', [0]);
        $client = $this->getMockClient(200, 'weather/yahoo_empty.xml');
        $yahooCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient($client);
        $result = $yahooCrawler->crawl();
        $this->assertEquals(new \ArrayObject(), $result);
        $this->assertFalse($yahooCrawler->getStationsStatus()[0]);
    }

    /**
     * @return array
     */
    public function invalidUnitProvider()
    {
        return [
            ['a'],
            [1],
            [true],
            [false],
            ['c'],
            ['f'],
            [1.1],
            [0],
        ];
    }

    /**
     * @return \ArrayObject
     */
    private function expectedValid()
    {

        return new \ArrayObject([
            0 => new \ArrayObject(
                [
                    'build_date' => new \DateTime('Fri, 06 Jan 2017 10:05 AM EET'),
                    'city' => 'City',
                    'geo_lat' => 44.176949,
                    'geo_long' => 28.653219,
                    'current_temp' => -1,
                    'current_code' => 15,
                    'current_wind_chill' => 12,
                    'current_wind_direction' => 25,
                    'current_wind_speed' => 104.6,
                    'current_atm_humidity' => 96,
                    'current_atm_pressure' => 33965.49,
                    'current_atm_visibility' => 7.4,
                    'current_astro_sunrise' => '7:41 am',
                    'current_astro_sunset' => '4:43 pm',
                    'forecast_date_0' => new \DateTime('06 Jan 2017'),
                    'forecast_code_0' => 5,
                    'forecast_high_0' => 4,
                    'forecast_low_0' => -6,
                    'forecast_date_1' => new \DateTime('07 Jan 2017'),
                    'forecast_code_1' => 15,
                    'forecast_high_1' => -7,
                    'forecast_low_1' => -12,
                    'forecast_date_2' => new \DateTime('08 Jan 2017'),
                    'forecast_code_2' => 23,
                    'forecast_high_2' => -10,
                    'forecast_low_2' => -11,
                    'forecast_date_3' => new \DateTime('09 Jan 2017'),
                    'forecast_code_3' => 28,
                    'forecast_high_3' => -10,
                    'forecast_low_3' => -13,
                    'forecast_date_4' => new \DateTime('10 Jan 2017'),
                    'forecast_code_4' => 28,
                    'forecast_high_4' => -11,
                    'forecast_low_4' => -15,
                    'forecast_date_5' => new \DateTime('11 Jan 2017'),
                    'forecast_code_5' => 28,
                    'forecast_high_5' => -11,
                    'forecast_low_5' => -16,
                    'forecast_date_6' => new \DateTime('12 Jan 2017'),
                    'forecast_code_6' => 28,
                    'forecast_high_6' => -5,
                    'forecast_low_6' => -12,
                    'forecast_date_7' => new \DateTime('13 Jan 2017'),
                    'forecast_code_7' => 30,
                    'forecast_high_7' => 2,
                    'forecast_low_7' => -5,
                    'forecast_date_8' => new \DateTime('14 Jan 2017'),
                    'forecast_code_8' => 23,
                    'forecast_high_8' => 5,
                    'forecast_low_8' => 0,
                    'forecast_date_9' => new \DateTime('15 Jan 2017'),
                    'forecast_code_9' => 28,
                    'forecast_high_9' => 1,
                    'forecast_low_9' => -3,
                ]
            )
        ]);
    }
}

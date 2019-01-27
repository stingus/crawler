<?php

namespace Stingus\Crawler\Test\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Stingus\Crawler\Weather\OpenWeatherCrawler;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

/**
 * Class OpenWeatherCrawlerTest
 * @group Unit
 */
class OpenWeatherCrawlerTest extends TestCase
{
    use MockClientTrait;

    /**
     * @param $unit
     * @param $expectedUrl
     *
     * @dataProvider unitsProvider
     */
    public function testUnits($unit, $expectedUrl)
    {
        $openWeatherCrawler = new OpenWeatherCrawler('http://example.com', $unit, [0]);
        $mock = new MockHandler(
            [
                new Response(200),
                new Response(200),
            ]
        );
        $stack = HandlerStack::create($mock);
        $history = [];
        $stack->push(Middleware::history($history));

        $openWeatherCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient(new Client(['handler' => $stack]))
            ->crawl();

        /** @var Request $history1 */
        $history1 = $history[0]['request'];
        /** @var Request $history2 */
        $history2 = $history[1]['request'];
        $this->assertCount(2, $history);
        $this->assertEquals(sprintf($expectedUrl, 'weather'), $history1->getUri());
        $this->assertEquals(sprintf($expectedUrl, 'forecast'), $history2->getUri());
    }

    public function testApiKeyIsSet()
    {
        $openWeatherCrawler = new OpenWeatherCrawler('http://example.com', 'C', [0], 'abcd');
        $mock = new MockHandler(
            [
                new Response(200),
                new Response(200),
            ]
        );
        $stack = HandlerStack::create($mock);
        $history = [];
        $stack->push(Middleware::history($history));

        $openWeatherCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient(new Client(['handler' => $stack]))
            ->crawl();

        /** @var Request $history1 */
        $history1 = $history[0]['request'];
        /** @var Request $history2 */
        $history2 = $history[1]['request'];
        $this->assertCount(2, $history);
        $this->assertEquals('http://example.com/weather?APPID=abcd&id=0&units=metric&mode=xml', $history1->getUri());
        $this->assertEquals('http://example.com/forecast?APPID=abcd&id=0&units=metric&mode=xml', $history2->getUri());
    }

    public function testLangIsSet()
    {
        $openWeatherCrawler = new OpenWeatherCrawler('http://example.com', 'C', [0], null, 'lang');
        $mock = new MockHandler(
            [
                new Response(200),
                new Response(200),
            ]
        );
        $stack = HandlerStack::create($mock);
        $history = [];
        $stack->push(Middleware::history($history));

        $openWeatherCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient(new Client(['handler' => $stack]))
            ->crawl();

        /** @var Request $history1 */
        $history1 = $history[0]['request'];
        /** @var Request $history2 */
        $history2 = $history[1]['request'];
        $this->assertCount(2, $history);
        $this->assertEquals('http://example.com/weather?id=0&units=metric&mode=xml&lang=lang', $history1->getUri());
        $this->assertEquals('http://example.com/forecast?id=0&units=metric&mode=xml&lang=lang', $history2->getUri());
    }

    /**
     * @dataProvider invalidUnitProvider
     * @expectedException \Stingus\Crawler\Exceptions\Weather\InvalidWeatherUnitException
     *
     * @param mixed $unit
     */
    public function testInvalidUnit($unit)
    {
        new OpenWeatherCrawler('http://example.com', $unit, []);
    }

    public function testOpenWeatherCrawlValid()
    {
        $openWeatherCrawler = new OpenWeatherCrawler('http://example.com', 'C', [0]);
        $mock = new MockHandler(
            [
                new Response(
                    200,
                    [],
                    file_get_contents(__DIR__ . '/../fixtures/weather/openweather_weather_valid.xml')
                ),
                new Response(
                    200,
                    [],
                    file_get_contents(__DIR__ . '/../fixtures/weather/openweather_forecast_valid.xml')
                ),
            ]
        );
        $stack = HandlerStack::create($mock);

        $openWeatherCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient(new Client(['handler' => $stack]));
        $result = $openWeatherCrawler->crawl();
        $this->assertEquals(new \ArrayObject([0 => $this->expectedValid()]), $result);
        $this->assertTrue($openWeatherCrawler->getStationsStatus()[0]);
    }

    public function testOpenWeatherCrawlTwoStationsValid()
    {
        $openWeatherCrawler = new OpenWeatherCrawler('http://example.com', 'C', [0, 1]);
        $responseWeather = new Response(
            200,
            [],
            file_get_contents(__DIR__ . '/../fixtures/weather/openweather_weather_valid.xml')
        );
        $responseForecast = new Response(
            200,
            [],
            file_get_contents(__DIR__ . '/../fixtures/weather/openweather_forecast_valid.xml')
        );
        $mock = new MockHandler(
            [
                $responseWeather,
                $responseForecast,
                $responseWeather,
                $responseForecast,
            ]
        );
        $stack = HandlerStack::create($mock);

        $openWeatherCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient(new Client(['handler' => $stack]));
        $result = $openWeatherCrawler->crawl();
        $this->assertEquals(
            new \ArrayObject([
                0 => $this->expectedValid(),
                1 => $this->expectedValid(),
            ]),
            $result
        );
        $this->assertTrue($openWeatherCrawler->getStationsStatus()[0]);
        $this->assertTrue($openWeatherCrawler->getStationsStatus()[1]);
    }

    public function testOpenWeatherCrawlInvalidStation()
    {
        $openWeatherCrawler = new OpenWeatherCrawler('http://example.com', 'C', [0]);
        $mock = new MockHandler(
            [
                new Response(
                    200,
                    [],
                    file_get_contents(__DIR__ . '/../fixtures/weather/openweather_invalid_station.xml')
                ),
                new Response(200),
            ]
        );
        $stack = HandlerStack::create($mock);
        $openWeatherCrawler
            ->setDomCrawler(new DomCrawler())
            ->setClient(new Client(['handler' => $stack]));

        $result = $openWeatherCrawler->crawl();
        $this->assertEquals(new \ArrayObject(), $result);
        $this->assertFalse($openWeatherCrawler->getStationsStatus()[0]);
    }

    public function unitsProvider()
    {
        return [
            ['C', 'http://example.com/%s?id=0&units=metric&mode=xml'],
            ['F', 'http://example.com/%s?id=0&units=imperial&mode=xml'],
        ];
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

        return new \ArrayObject(
            [
                'city' => 'City',
                'geo_lat' => 12.34,
                'geo_long' => 56.78,
                'current_temp' => 0,
                'current_code' => 701,
                'current_icon' => '50d',
                'current_descr' => 'mist',
                'current_wind_direction' => 90,
                'current_wind_speed' => 1.5,
                'current_atm_humidity' => 100,
                'current_atm_pressure' => 1009,
                'current_atm_visibility' => 2500.0,
                'current_astro_sunrise' => '5:39 am',
                'current_astro_sunset' => '3:17 pm',
                'forecast_date_0' => new \DateTime('2019-01-27T00:00:00'),
                'forecast_code_0' => 800,
                'forecast_icon_0' => '01d',
                'forecast_descr_0' => 'clear sky',
                'forecast_high_0' => 2,
                'forecast_low_0' => -5,
                'forecast_date_1' => new \DateTime('2019-01-28T00:00:00'),
                'forecast_code_1' => 500,
                'forecast_icon_1' => '10d',
                'forecast_descr_1' => 'light rain',
                'forecast_high_1' => 5,
                'forecast_low_1' => -5,
                'forecast_date_2' => new \DateTime('2019-01-29T00:00:00'),
                'forecast_code_2' => 500,
                'forecast_icon_2' => '10d',
                'forecast_descr_2' => 'light rain',
                'forecast_high_2' => 7,
                'forecast_low_2' => -3,
                'forecast_date_3' => new \DateTime('2019-01-30T00:00:00'),
                'forecast_code_3' => 500,
                'forecast_icon_3' => '10d',
                'forecast_descr_3' => 'light rain',
                'forecast_high_3' => 12,
                'forecast_low_3' => 2,
                'forecast_date_4' => new \DateTime('2019-01-31T00:00:00'),
                'forecast_code_4' => 500,
                'forecast_icon_4' => '10d',
                'forecast_descr_4' => 'light rain',
                'forecast_high_4' => 11,
                'forecast_low_4' => 2,
            ]
        );
    }
}

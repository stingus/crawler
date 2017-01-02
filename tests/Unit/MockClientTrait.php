<?php

namespace Stingus\Crawler\Test\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

/**
 * Class MockClientTrait
 *
 * @package Stingus\Crawler\Test\Unit
 */
trait MockClientTrait
{
    /**
     * @param $responseCode
     * @param $file
     *
     * @return Client
     */
    private function getMockClient($responseCode, $file = null)
    {
        $content = null;
        if (null !== $file) {
            $content = file_get_contents(__DIR__ . '/../fixtures/' . $file);
        }
        $mock = new MockHandler(
            [
                new Response($responseCode, [], $content),
            ]
        );
        $handler = HandlerStack::create($mock);

        return new Client(['handler' => $handler]);
    }
}

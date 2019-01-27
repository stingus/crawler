<?php

namespace Stingus\Crawler\Test\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;

/**
 * Class MockClientTrait
 *
 * @package Stingus\Crawler\Test\Unit
 */
trait MockClientTrait
{
    /**
     * @param int    $responseCode
     * @param string $file
     * @param array  $container
     *
     * @return Client
     */
    private function getMockClient($responseCode, $file = null, &$container = [])
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
        $history = Middleware::history($container);
        $stack = HandlerStack::create($mock);
        $stack->push($history);

        return new Client(['handler' => $stack]);
    }
}

<?php

namespace HyperfTest\Trait;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\ResponseInterface;

trait GuzzleClientMockTrait
{
    public function getClient(RequestExceptionInterface|ResponseInterface ...$responses): Client
    {
        $mockResponse = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mockResponse);

        return new Client(['handler' => $handlerStack]);
    }
}

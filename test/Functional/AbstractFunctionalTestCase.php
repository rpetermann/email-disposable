<?php

namespace HyperfTest\Functional;

use Hyperf\Testing\Client;
use Hyperf\Testing\TestCase;

use function Hyperf\Support\make;

abstract class AbstractFunctionalTestCase extends TestCase
{
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = make(Client::class);
    }
}

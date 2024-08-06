<?php

namespace HyperfTest\Functional\Controller;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Testing\Concerns\InteractsWithContainer;
use HyperfTest\Functional\AbstractFunctionalTestCase;
use HyperfTest\Trait\GuzzleClientMockTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;

class CheckEmailDisposableControllerTest extends AbstractFunctionalTestCase
{
    use InteractsWithContainer;
    use GuzzleClientMockTrait;

    private const URL = '/v1/email-disposable/check';

    private ClientFactory|MockObject $clientFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientFactory = $this->getMockBuilder(ClientFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->swap(ClientFactory::class, $this->clientFactory);
    }

    public static function dataProviderShouldReturnErrorWhenPayloadIsInvalid(): \Generator
    {
        yield 'empty payload' => [
            [],
        ];
        yield 'missing evaluator' => [
            ['emailOrDomain' => 'mock.com'],
        ];
        yield 'missing emailOrDomain' => [
            ['evaluator' => 'allOrNothing'],
        ];
    }

    #[DataProvider('dataProviderShouldReturnErrorWhenPayloadIsInvalid')]
    public function testShouldReturnErrorWhenPayloadIsInvalid(array $options): void
    {
        $response = $this->client->request('GET', self::URL, $options);
        $responseJson = json_decode((string) $response->getBody()->getContents(), true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertEqualsCanonicalizing([
            'code' => 422,
            'error' => 'parameters evaluator and emailOrDomain are required',
        ], $responseJson);
    }

    public function testShouldReturnErrorWhenEvaluatorIsInvalid(): void
    {
        $options = [
            'query' => [
                'evaluator' => 'invalidEvaluator',
                'emailOrDomain' => 'mock.com',
            ],
        ];
        $response = $this->client->request('GET', self::URL, $options);
        $responseJson = json_decode((string) $response->getBody()->getContents(), true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertEqualsCanonicalizing([
            'code' => 422,
            'error' => 'Invalid evaluator',
        ], $responseJson);
    }

    public function testShouldReturnErrorWhenAllRequestsFail(): void
    {
        $guzzleClient = $this->getClient(...[
            new RequestException('Mock error', new Request('GET', 'mock-provider-1')),
            new RequestException('Mock error', new Request('GET', 'mock-provider-2')),
        ]);
        $this->clientFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturn($guzzleClient);

        $options = [
            'query' => [
                'evaluator' => 'allOrNothing',
                'emailOrDomain' => 'mock.com',
            ],
        ];
        $response = $this->client->request('GET', self::URL, $options);
        $responseJson = json_decode((string) $response->getBody()->getContents(), true);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertEqualsCanonicalizing([
            'code' => 500,
            'error' => 'Requests failed',
        ], $responseJson);
    }

    public function testShouldSuccessfullyCheckEmailDisposable(): void
    {
        $response1 = [
            'disposable' => true,
        ];
        $response2 = [
            'format' => true,
            'domain' => 'bacaki.com',
            'disposable' => true,
            'dns' => true,
        ];

        $guzzleClient = $this->getClient(...[
            new GuzzleResponse(200, [], json_encode($response1)),
            new GuzzleResponse(200, [], json_encode($response2)),
        ]);
        $this->clientFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturn($guzzleClient);

        $options = [
            'query' => [
                'evaluator' => 'allOrNothing',
                'emailOrDomain' => 'mock.com',
            ],
        ];
        $response = $this->client->request('GET', self::URL, $options);
        $responseJson = json_decode((string) $response->getBody()->getContents(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertEqualsCanonicalizing([
            'emailOrDomain' => 'mock.com',
            'disposable' => true,
            'outputs' => [
                ['provider' => 'debounce', 'disposable' => true],
                ['provider' => 'disify', 'disposable' => true],
            ],
        ], $responseJson);
    }
}

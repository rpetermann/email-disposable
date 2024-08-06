<?php

namespace HyperfTest\Unit\UseCase\CheckEmailDisposable\Provider;

use App\UseCase\CheckEmailDisposable\Provider\DebounceIOEmailDisposableProvider;
use App\UseCase\CheckEmailDisposable\Provider\Dto\CheckEmailDisposableProviderInput;
use App\UseCase\CheckEmailDisposable\Provider\Dto\CheckEmailDisposableProviderOutput;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Hyperf\Guzzle\ClientFactory;
use HyperfTest\Unit\AbstractUnitTestCase;
use HyperfTest\Trait\GuzzleClientMockTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;

use function Hyperf\Support\make;

class DebounceIOEmailDisposableProviderTest extends AbstractUnitTestCase
{
    use GuzzleClientMockTrait;

    private ClientFactory|MockObject $clientFactory;
    private DebounceIOEmailDisposableProvider $debounceIOEmailDisposableProvider;

    public function testIsDisposableShouldReturnNullWhenRequestFails(): void
    {
        $guzzleClient = $this->getClient(...[
            new RequestException('Mock error', new Request('GET', 'mock')),
        ]);
        $this->clientFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($guzzleClient);

        $input = new CheckEmailDisposableProviderInput('mock.com');
        $response = $this->debounceIOEmailDisposableProvider->isDisposable($input);

        $this->assertInstanceOf(CheckEmailDisposableProviderOutput::class, $response);
        $this->assertNull($response->disposable);
        $this->assertSame('debounce', $response->provider);
    }

    public function testIsDisposableShouldReturnNullWhenResponseDontContainsInformation(): void
    {
        $debounceResponse = [
            'mock' => 'response',
        ];
        $guzzleClient = $this->getClient(...[
            new GuzzleResponse(200, [], json_encode($debounceResponse)),
        ]);
        $this->clientFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($guzzleClient);

        $input = new CheckEmailDisposableProviderInput('mock.com');
        $response = $this->debounceIOEmailDisposableProvider->isDisposable($input);

        $this->assertInstanceOf(CheckEmailDisposableProviderOutput::class, $response);
        $this->assertNull($response->disposable);
        $this->assertSame('debounce', $response->provider);
    }

    public static function dataProviderIsDisposableShouldSuccessfullyProcess(): \Generator
    {
        yield 'disposable = true as boolean' => [
            'debounceResponse' => [
                'disposable' => true,
            ],
            'expectedResponse' => true,
        ];
        yield 'disposable = "true" as string' => [
            'debounceResponse' => [
                'disposable' => 'true',
            ],
            'expectedResponse' => true,
        ];
        yield 'disposable = 1 as integer' => [
            'debounceResponse' => [
                'disposable' => 1,
            ],
            'expectedResponse' => true,
        ];
        yield 'disposable = false as boolean' => [
            'debounceResponse' => [
                'disposable' => false,
            ],
            'expectedResponse' => false,
        ];
        yield 'disposable = "false" as string' => [
            'debounceResponse' => [
                'disposable' => 'false',
            ],
            'expectedResponse' => false,
        ];
        yield 'disposable = 0 as integer' => [
            'debounceResponse' => [
                'disposable' => 0,
            ],
            'expectedResponse' => false,
        ];
    }

    #[DataProvider('dataProviderIsDisposableShouldSuccessfullyProcess')]
    public function testIsDisposableShouldSuccessfullyProcess(array $debounceResponse, bool $expectedResponse): void
    {
        $guzzleClient = $this->getClient(...[
            new GuzzleResponse(200, [], json_encode($debounceResponse)),
        ]);
        $this->clientFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($guzzleClient);

        $input = new CheckEmailDisposableProviderInput('mock.com');
        $response = $this->debounceIOEmailDisposableProvider->isDisposable($input);

        $this->assertInstanceOf(CheckEmailDisposableProviderOutput::class, $response);
        $this->assertSame($expectedResponse, $response->disposable);
        $this->assertSame('debounce', $response->provider);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientFactory = $this->getMockBuilder(ClientFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->debounceIOEmailDisposableProvider = make(
            DebounceIOEmailDisposableProvider::class,
            [
                $this->clientFactory,
            ],
        );
    }
}

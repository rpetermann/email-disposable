<?php

namespace HyperfTest\Unit\UseCase\CheckEmailDisposable\Provider;

use App\UseCase\CheckEmailDisposable\Provider\DisifyEmailDisposableProvider;
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

class DisifyEmailDisposableProviderTest extends AbstractUnitTestCase
{
    use GuzzleClientMockTrait;

    private ClientFactory|MockObject $clientFactory;
    private DisifyEmailDisposableProvider $disifyEmailDisposableProvider;

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
        $response = $this->disifyEmailDisposableProvider->isDisposable($input);

        $this->assertInstanceOf(CheckEmailDisposableProviderOutput::class, $response);
        $this->assertNull($response->disposable);
        $this->assertSame('disify', $response->provider);
    }

    public function testIsDisposableShouldReturnNullWhenResponseDontContainsInformation(): void
    {
        $disifyResponse = [
            'mock' => 'response',
        ];
        $guzzleClient = $this->getClient(...[
            new GuzzleResponse(200, [], json_encode($disifyResponse)),
        ]);
        $this->clientFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($guzzleClient);

        $input = new CheckEmailDisposableProviderInput('mock.com');
        $response = $this->disifyEmailDisposableProvider->isDisposable($input);

        $this->assertInstanceOf(CheckEmailDisposableProviderOutput::class, $response);
        $this->assertNull($response->disposable);
        $this->assertSame('disify', $response->provider);
    }

    public static function dataProviderIsDisposableShouldSuccessfullyProcess(): \Generator
    {
        yield 'disposable = true as boolean' => [
            'disifyResponse' => [
                'format' => true,
                'domain' => 'bacaki.com',
                'disposable' => true,
                'dns' => true,
            ],
            'expectedResponse' => true,
        ];
        yield 'disposable = "true" as string' => [
            'disifyResponse' => [
                'format' => true,
                'domain' => 'bacaki.com',
                'disposable' => 'true',
                'dns' => true,
            ],
            'expectedResponse' => true,
        ];
        yield 'disposable = 1 as integer' => [
            'disifyResponse' => [
                'format' => true,
                'domain' => 'bacaki.com',
                'disposable' => 1,
                'dns' => true,
            ],
            'expectedResponse' => true,
        ];
        yield 'disposable = false as boolean' => [
            'disifyResponse' => [
                'format' => true,
                'domain' => 'gmail.com',
                'disposable' => false,
                'dns' => true,
                'whitelist' => true,
            ],
            'expectedResponse' => false,
        ];
        yield 'disposable = "false" as string' => [
            'disifyResponse' => [
                'format' => true,
                'domain' => 'gmail.com',
                'disposable' => 'false',
                'dns' => true,
                'whitelist' => true,
            ],
            'expectedResponse' => false,
        ];
        yield 'disposable = 0 as integer' => [
            'disifyResponse' => [
                'format' => true,
                'domain' => 'gmail.com',
                'disposable' => 0,
                'dns' => true,
                'whitelist' => true,
            ],
            'expectedResponse' => false,
        ];
    }

    #[DataProvider('dataProviderIsDisposableShouldSuccessfullyProcess')]
    public function testIsDisposableShouldSuccessfullyProcess(array $disifyResponse, bool $expectedResponse): void
    {
        $guzzleClient = $this->getClient(...[
            new GuzzleResponse(200, [], json_encode($disifyResponse)),
        ]);
        $this->clientFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($guzzleClient);

        $input = new CheckEmailDisposableProviderInput('mock.com');
        $response = $this->disifyEmailDisposableProvider->isDisposable($input);

        $this->assertInstanceOf(CheckEmailDisposableProviderOutput::class, $response);
        $this->assertSame($expectedResponse, $response->disposable);
        $this->assertSame('disify', $response->provider);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientFactory = $this->getMockBuilder(ClientFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->disifyEmailDisposableProvider = make(
            DisifyEmailDisposableProvider::class,
            [
                $this->clientFactory,
            ],
        );
    }
}

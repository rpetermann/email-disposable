<?php

namespace App\UseCase\CheckEmailDisposable\Provider;

use App\UseCase\CheckEmailDisposable\Provider\Dto\CheckEmailDisposableProviderInput;
use App\UseCase\CheckEmailDisposable\Provider\Dto\CheckEmailDisposableProviderOutput;
use Hyperf\Guzzle\ClientFactory;

class DisifyEmailDisposableProvider extends AbstractEmailDisposableProvider
{
    private const PROVIDER_NAME = 'disify';
    private const BASE_URI = 'https://disify.com';

    public function __construct(
        private ClientFactory $clientFactory,
    ) {
    }

    public function isDisposable(CheckEmailDisposableProviderInput $input): CheckEmailDisposableProviderOutput
    {
        $data = $this->sendRequest($input);
        $disposable = isset($data['disposable'])
            ? filter_var($data['disposable'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            : null;

        return new CheckEmailDisposableProviderOutput(
            provider: self::PROVIDER_NAME,
            disposable: $disposable,
        );
    }

    private function sendRequest(CheckEmailDisposableProviderInput $input): ?array
    {
        $client = $this->clientFactory->create([
            'base_uri' => self::BASE_URI,
            'timeout' => self::DEFAULT_REQUEST_TIMEOUT_IN_SECONDS,
        ]);

        $uri = '/api/domain/' . $this->getDomain($input);
        try {
            $response = $client->get($uri);

            return json_decode($response->getBody()->getContents(), true) ?: null;
        } catch (\Exception) {
            return null;
        }
    }
}

<?php

namespace App\UseCase\CheckEmailDisposable\Provider;

use App\UseCase\CheckEmailDisposable\Provider\Dto\CheckEmailDisposableProviderInput;

abstract class AbstractEmailDisposableProvider implements EmailDisposableProviderInterface
{
    protected const DEFAULT_CAN_PROCESS = true;
    protected const DEFAULT_REQUEST_TIMEOUT_IN_SECONDS = 1;

    public function canProcess(CheckEmailDisposableProviderInput $input): bool
    {
        return static::DEFAULT_CAN_PROCESS;
    }

    protected function getDomain(CheckEmailDisposableProviderInput $input): string
    {
        if (!str_contains($input->emailOrDomain, '@')) {
            return $input->emailOrDomain;
        }

        return explode('@', $input->emailOrDomain)[1];
    }

    protected function getEmail(CheckEmailDisposableProviderInput $input): string
    {
        if (str_contains($input->emailOrDomain, '@')) {
            return $input->emailOrDomain;
        }

        return "info@{$input->emailOrDomain}";
    }
}

<?php

namespace App\UseCase\CheckEmailDisposable\Provider\Dto;

class CheckEmailDisposableProviderOutput
{
    public function __construct(
        public readonly string $provider,
        public readonly ?bool $disposable,
    ) {
    }
}

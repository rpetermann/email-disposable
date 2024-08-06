<?php

namespace App\UseCase\CheckEmailDisposable\Provider\Dto;

class CheckEmailDisposableProviderInput
{
    public function __construct(
        public readonly string $emailOrDomain,
    ) {
    }
}

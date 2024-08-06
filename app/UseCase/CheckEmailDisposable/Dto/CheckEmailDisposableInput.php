<?php

namespace App\UseCase\CheckEmailDisposable\Dto;

class CheckEmailDisposableInput
{
    public function __construct(
        public readonly string $evaluator,
        public readonly string $emailOrDomain,
    )
    {
    }
}

<?php

namespace App\UseCase\CheckEmailDisposable\Provider;

use App\UseCase\CheckEmailDisposable\Provider\Dto\CheckEmailDisposableProviderInput;
use App\UseCase\CheckEmailDisposable\Provider\Dto\CheckEmailDisposableProviderOutput;

interface EmailDisposableProviderInterface
{
    public function canProcess(CheckEmailDisposableProviderInput $input): bool;

    public function isDisposable(CheckEmailDisposableProviderInput $input): CheckEmailDisposableProviderOutput;
}

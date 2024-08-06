<?php

namespace App\UseCase\CheckEmailDisposable\Evaluator;

use App\UseCase\CheckEmailDisposable\Dto\CheckEmailDisposableOutputCollection;

interface EvaluatorInterface
{
    public function evaluate(CheckEmailDisposableOutputCollection $outputs): bool;
}

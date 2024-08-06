<?php

namespace App\UseCase\CheckEmailDisposable\Evaluator;

use App\UseCase\CheckEmailDisposable\Dto\CheckEmailDisposableOutputCollection;
use App\UseCase\CheckEmailDisposable\Provider\Dto\CheckEmailDisposableProviderOutput;

class AllOrNothingEvaluator implements EvaluatorInterface
{
    public function evaluate(CheckEmailDisposableOutputCollection $outputs): bool
    {
        foreach ($outputs as $output) {
            if (!$output instanceof CheckEmailDisposableProviderOutput) {
                continue;
            }
            if (false === $output->disposable) {
                return false;
            }
        }

        return true;
    }
}

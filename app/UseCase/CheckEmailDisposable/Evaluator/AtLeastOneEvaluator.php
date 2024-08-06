<?php

namespace App\UseCase\CheckEmailDisposable\Evaluator;

use App\UseCase\CheckEmailDisposable\Dto\CheckEmailDisposableOutputCollection;
use App\UseCase\CheckEmailDisposable\Provider\Dto\CheckEmailDisposableProviderOutput;

class AtLeastOneEvaluator implements EvaluatorInterface
{
    public function evaluate(CheckEmailDisposableOutputCollection $outputs): bool
    {
        foreach ($outputs as $output) {
            if (!$output instanceof CheckEmailDisposableProviderOutput) {
                continue;
            }
            if ($output->disposable) {
                return true;
            }
        }

        return false;
    }
}

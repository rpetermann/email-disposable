<?php

namespace App\UseCase\CheckEmailDisposable;

use App\UseCase\CheckEmailDisposable\Dto\CheckEmailDisposableInput;
use App\UseCase\CheckEmailDisposable\Dto\CheckEmailDisposableOutput;
use App\UseCase\CheckEmailDisposable\Dto\CheckEmailDisposableOutputCollection;
use App\UseCase\CheckEmailDisposable\Evaluator\AllOrNothingEvaluator;
use App\UseCase\CheckEmailDisposable\Evaluator\AtLeastOneEvaluator;
use App\UseCase\CheckEmailDisposable\Evaluator\EvaluatorInterface;
use App\UseCase\CheckEmailDisposable\Exception\InvalidEvaluatorException;
use App\UseCase\CheckEmailDisposable\Exception\InvalidEvaluatorInterfaceException;
use App\UseCase\CheckEmailDisposable\Exception\RequestsFailedException;
use App\UseCase\CheckEmailDisposable\Provider\DebounceIOEmailDisposableProvider;
use App\UseCase\CheckEmailDisposable\Provider\DisifyEmailDisposableProvider;
use App\UseCase\CheckEmailDisposable\Provider\Dto\CheckEmailDisposableProviderInput;
use App\UseCase\CheckEmailDisposable\Provider\EmailDisposableProviderInterface;
use Hyperf\Coroutine\Parallel;

use function Hyperf\Support\make;

class CheckEmailDisposableStrategy
{
    private const PROVIDERS = [
        DebounceIOEmailDisposableProvider::class,
        DisifyEmailDisposableProvider::class,
    ];
    private const EVALUATORS = [
        'allOrNothing' => AllOrNothingEvaluator::class,
        'atLeastOne' => AtLeastOneEvaluator::class,
    ];

    public function check(CheckEmailDisposableInput $input): CheckEmailDisposableOutput
    {
        $this->validate($input);
        $outputs = $this->process($input);

        return new CheckEmailDisposableOutput(
            emailOrDomain: $input->emailOrDomain,
            disposable: $this->evaluate($input, $outputs),
            outputs: $outputs,
        );
    }

    private function validate(CheckEmailDisposableInput $input): void
    {
        $evaluator = self::EVALUATORS[$input->evaluator] ?? null;
        if ($evaluator === null) {
            throw new InvalidEvaluatorException();
        }
        if (!is_a($evaluator, EvaluatorInterface::class, true)) {
            throw new InvalidEvaluatorInterfaceException();
        }
    }

    private function process(CheckEmailDisposableInput $input): CheckEmailDisposableOutputCollection
    {
        $parallel = new Parallel();
        foreach (self::PROVIDERS as $className) {
            $providerInput = new CheckEmailDisposableProviderInput($input->emailOrDomain);
            $instance = $this->getProcessorInstance($className, $providerInput);
            if ($instance === null) {
                continue;
            }
            $parallel->add(fn () => $instance->isDisposable($providerInput));
        }
        $outputs = new CheckEmailDisposableOutputCollection(
            outputs: $parallel->wait(),
        );
        if (!$outputs->hasOutput()) {
            throw new RequestsFailedException();
        }

        return $outputs;
    }

    private function getProcessorInstance(string $className, CheckEmailDisposableProviderInput $providerInput): ?EmailDisposableProviderInterface
    {
        $instance = make($className);
        if (!$instance instanceof EmailDisposableProviderInterface) {
            return null;
        }
        if (!$instance->canProcess($providerInput)) {
            return null;
        }

        return $instance;
    }

    private function evaluate(CheckEmailDisposableInput $input, CheckEmailDisposableOutputCollection $outputs): bool
    {
        $evaluatorClass = self::EVALUATORS[$input->evaluator];
        $evaluatorInstance = make($evaluatorClass);

        return $evaluatorInstance->evaluate($outputs);
    }
}

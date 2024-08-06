<?php

namespace HyperfTest\Unit\UseCase\CheckEmailDisposable\Evaluator;

use App\UseCase\CheckEmailDisposable\Dto\CheckEmailDisposableOutputCollection;
use App\UseCase\CheckEmailDisposable\Evaluator\AllOrNothingEvaluator;
use App\UseCase\CheckEmailDisposable\Provider\Dto\CheckEmailDisposableProviderOutput;
use HyperfTest\Unit\AbstractUnitTestCase;

class AllOrNothingEvaluatorTest extends AbstractUnitTestCase
{
    private AllOrNothingEvaluator $allOrNothingEvaluator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->allOrNothingEvaluator = $this->getContainer()->get(AllOrNothingEvaluator::class);
    }

    public function testEvaluateShouldReturnTrueWhenOneOutputIsNull(): void
    {
        $output1 = new CheckEmailDisposableProviderOutput(
            'mock provider 1',
            null,
        );
        $output2 = new CheckEmailDisposableProviderOutput(
            'mock provider 2',
            true,
        );
        $outputs = new CheckEmailDisposableOutputCollection([
            $output1,
            $output2,
        ]);

        $response = $this->allOrNothingEvaluator->evaluate($outputs);

        $this->assertTrue($response);
    }

    public function testEvaluateShouldReturnFalseWhenOneOutputIsFalse(): void
    {
        $output1 = new CheckEmailDisposableProviderOutput(
            'mock provider 1',
            false,
        );
        $output2 = new CheckEmailDisposableProviderOutput(
            'mock provider 2',
            true,
        );
        $outputs = new CheckEmailDisposableOutputCollection([
            $output1,
            $output2,
        ]);

        $response = $this->allOrNothingEvaluator->evaluate($outputs);

        $this->assertFalse($response);
    }

    public function testEvaluateShouldReturnFalseWhenHasFalseAndNullOutputs(): void
    {
        $output1 = new CheckEmailDisposableProviderOutput(
            'mock provider 1',
            false,
        );
        $output2 = new CheckEmailDisposableProviderOutput(
            'mock provider 2',
            null,
        );
        $outputs = new CheckEmailDisposableOutputCollection([
            $output1,
            $output2,
        ]);

        $response = $this->allOrNothingEvaluator->evaluate($outputs);

        $this->assertFalse($response);
    }

    public function testEvaluateShouldReturnTrueWhenOneAllOutputsAreTrue(): void
    {
        $output1 = new CheckEmailDisposableProviderOutput(
            'mock provider 1',
            true,
        );
        $output2 = new CheckEmailDisposableProviderOutput(
            'mock provider 2',
            true,
        );
        $outputs = new CheckEmailDisposableOutputCollection([
            $output1,
            $output2,
        ]);

        $response = $this->allOrNothingEvaluator->evaluate($outputs);

        $this->assertTrue($response);
    }
}

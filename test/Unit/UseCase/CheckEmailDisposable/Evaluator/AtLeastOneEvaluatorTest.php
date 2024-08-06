<?php

namespace HyperfTest\Unit\UseCase\CheckEmailDisposable\Evaluator;

use App\UseCase\CheckEmailDisposable\Dto\CheckEmailDisposableOutputCollection;
use App\UseCase\CheckEmailDisposable\Evaluator\AtLeastOneEvaluator;
use App\UseCase\CheckEmailDisposable\Provider\Dto\CheckEmailDisposableProviderOutput;
use HyperfTest\Unit\AbstractUnitTestCase;

class AtLeastOneEvaluatorTest extends AbstractUnitTestCase
{
    private AtLeastOneEvaluator $atLeastOneEvaluator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->atLeastOneEvaluator = $this->getContainer()->get(AtLeastOneEvaluator::class);
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

        $response = $this->atLeastOneEvaluator->evaluate($outputs);

        $this->assertTrue($response);
    }

    public function testEvaluateShouldReturnTrueWhenAtLeastOutputIsTrue(): void
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

        $response = $this->atLeastOneEvaluator->evaluate($outputs);

        $this->assertTrue($response);
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

        $response = $this->atLeastOneEvaluator->evaluate($outputs);

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

        $response = $this->atLeastOneEvaluator->evaluate($outputs);

        $this->assertTrue($response);
    }
}

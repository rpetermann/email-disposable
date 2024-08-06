<?php

namespace App\UseCase\CheckEmailDisposable\Dto;

use App\UseCase\CheckEmailDisposable\Provider\Dto\CheckEmailDisposableProviderOutput;

class CheckEmailDisposableOutputCollection implements \IteratorAggregate
{
    public function __construct(
        public readonly array $outputs,
    ) {
        $this->validate();
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->outputs);
    }

    public function hasOutput(): bool
    {
        return !empty(
            array_filter($this->outputs, function (CheckEmailDisposableProviderOutput $output) {
                return is_bool($output->disposable);
            })
        );
    }

    private function validate(): void
    {
        if (empty($this->outputs)) {
            throw new \Exception('Outputs must be provided');
        }

        foreach ($this->outputs as $output) {
            if ($output instanceof CheckEmailDisposableProviderOutput) {
                continue;
            }
            throw new \Exception('Invalid output format');
        }
    }
}

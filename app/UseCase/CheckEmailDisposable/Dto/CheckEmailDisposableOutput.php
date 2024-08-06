<?php

namespace App\UseCase\CheckEmailDisposable\Dto;

use Hyperf\Contract\Jsonable;

class CheckEmailDisposableOutput implements Jsonable
{
    public function __construct(
        public readonly string $emailOrDomain,
        public readonly bool $disposable,
        public readonly CheckEmailDisposableOutputCollection $outputs,
    ) {
    }

    public function __toString(): string
    {
        return (string) json_encode([
            'emailOrDomain' => $this->emailOrDomain,
            'disposable' => $this->disposable,
            'outputs' => $this->outputs->outputs,
        ]);
    }
}

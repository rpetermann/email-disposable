<?php

namespace App\UseCase\CheckEmailDisposable\Dto;

class CheckEmailDisposableOutput implements \JsonSerializable
{
    public function __construct(
        public readonly string $emailOrDomain,
        public readonly bool $disposable,
        public readonly CheckEmailDisposableOutputCollection $outputs,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'emailOrDomain' => $this->emailOrDomain,
            'disposable' => $this->disposable,
            'outputs' => $this->outputs?->outputs,
        ];
    }
}

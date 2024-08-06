<?php

namespace App\Controller;

use App\Enum\StatusCodeEnum;
use App\UseCase\CheckEmailDisposable\CheckEmailDisposableStrategy;
use App\UseCase\CheckEmailDisposable\Dto\CheckEmailDisposableInput;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

class CheckEmailDisposableController extends AbstractController
{
    public function __construct(
        private readonly CheckEmailDisposableStrategy $checkEmailDisposableStrategy,
    ) {
    }

    public function check(): Psr7ResponseInterface
    {
        $payload = $this->request->all();
        if (!isset($payload['evaluator']) || !isset($payload['emailOrDomain'])) {
            throw new \Exception('parameters evaluator and emailOrDomain are required', StatusCodeEnum::UNPROCESSABLE_ENTITY->value);
        }

        $input = new CheckEmailDisposableInput(
            evaluator: $payload['evaluator'],
            emailOrDomain: $payload['emailOrDomain'],
        );

        return $this->response->json($this->checkEmailDisposableStrategy->check($input));
    }
}

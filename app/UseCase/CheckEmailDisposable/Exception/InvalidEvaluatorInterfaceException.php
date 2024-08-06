<?php

namespace App\UseCase\CheckEmailDisposable\Exception;

use App\Enum\StatusCodeEnum;

class InvalidEvaluatorInterfaceException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Invalid evaluator interface', StatusCodeEnum::UNPROCESSABLE_ENTITY->value);
    }
}

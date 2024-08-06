<?php

namespace App\UseCase\CheckEmailDisposable\Exception;

use App\Enum\StatusCodeEnum;

class InvalidEvaluatorException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Invalid evaluator', StatusCodeEnum::UNPROCESSABLE_ENTITY->value);
    }
}

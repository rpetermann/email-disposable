<?php

namespace App\UseCase\CheckEmailDisposable\Exception;

use App\Enum\StatusCodeEnum;

class RequestsFailedException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Requests failed', StatusCodeEnum::INTERNAL_SERVER_ERROR->value);
    }
}

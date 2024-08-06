<?php

namespace App\Enum;

enum StatusCodeEnum: int
{
    case BAD_REQUEST = 400;
    case UNPROCESSABLE_ENTITY = 422;
    case INTERNAL_SERVER_ERROR = 500;
}

<?php

namespace App\Exception\Handler;

use App\Enum\StatusCodeEnum;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $statusCode = $throwable->getCode() >= StatusCodeEnum::BAD_REQUEST->value 
            ? $throwable->getCode()
            : StatusCodeEnum::INTERNAL_SERVER_ERROR->value;
        $data = json_encode([
            'code' => $throwable->getCode(),
            'error' => $throwable->getMessage(),
        ], JSON_UNESCAPED_UNICODE);

        return $response
            ->withStatus($statusCode)
            ->withBody(new SwooleStream($data))
            ->withAddedHeader('Content-Type', 'applcation/json');
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}

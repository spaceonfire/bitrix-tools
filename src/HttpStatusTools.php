<?php

namespace spaceonfire\BitrixTools;

use CHTTP;
use Narrowspark\HttpStatus\Contract\Exception\HttpException as HttpExceptionContract;
use Narrowspark\HttpStatus\Exception\InternalServerErrorException;
use Narrowspark\HttpStatus\HttpStatus;
use Throwable;

abstract class HttpStatusTools extends HttpStatus
{
    /**
     * Устанавливает статус ответа и константу ошибки
     * @param Throwable $throwable
     */
    public static function catchError(Throwable $throwable): void
    {
        if ($throwable instanceof HttpExceptionContract) {
            $httpError = $throwable;
        } else {
            $httpError = new InternalServerErrorException($throwable->getMessage(), $throwable);
        }

        // Define error constant
        $statusCode = $httpError->getStatusCode();
        defined('ERROR_' . $statusCode) or define('ERROR_' . $statusCode, 'Y');

        // Set status
        CHTTP::SetStatus($statusCode . ' ' . static::getReasonPhrase($statusCode));
    }

    /**
     * Проверяет, был ли установлен статус ошибки HTTP
     * @return bool
     */
    public static function hasHttpError(): bool
    {
        $lastStatus = trim(CHTTP::GetLastStatus());

        if ($lastStatus === '') {
            return false;
        }

        try {
            $code = (int)explode(' ', $lastStatus, 1)[0];
            $code = static::filterStatusCode($code);
            return $code >= 400;
        } catch (Throwable $e) {
            return false;
        }
    }
}
